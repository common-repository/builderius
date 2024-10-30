<?php

namespace Builderius\Bundle\TemplateBundle\Migrations\v0_9_8;

use Builderius\Bundle\ReleaseBundle\EventListener\BuilderiusGlobalTypeSettingsTransformationBeforeReleaseImportListener;
use Builderius\Bundle\SettingBundle\Factory\BuilderiusGlobalSettingsSetFromPostFactory;
use Builderius\Bundle\SettingBundle\Model\BuilderiusGlobalSettingsSetInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Registration\BuilderiusGlobalSettingsSetPostType;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateContentProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTypeTaxonomy;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommit;
use Builderius\MooMoo\Platform\Bundle\MigrationBundle\Migration\Migration;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareTrait;

class BuilderiusTemplateBundleMigration implements Migration, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusTemplateFromPostFactory
     */
    private $templateFromPostFactory;

    /**
     * @var BuilderiusGlobalSettingsSetFromPostFactory
     */
    private $gssFromPostFactory;

    /**
     * @var BuilderiusTemplateTypesProviderInterface
     */
    private $templateTypesProvider;

    /**
     * @var BuilderiusSettingsRegistryInterface
     */
    private $settingsRegistry;

    /**
     * @var BuilderiusTemplateContentProviderInterface
     */
    private $contentProvider;

    /**
     * @return \WP_Query
     */
    protected function getWpQuery()
    {
        if ($this->wpQuery === null) {
            $this->wpQuery = $this->container->get('moomoo_query.wp_query');
        }

        return $this->wpQuery;
    }

    /**
     * @return BuilderiusTemplateFromPostFactory
     */
    public function getTemplateFromPostFactory()
    {
        if ($this->templateFromPostFactory === null) {
            $this->templateFromPostFactory = $this->container->get('builderius_template.factory.builderius_template_from_post');
        }

        return $this->templateFromPostFactory;
    }

    /**
     * @return BuilderiusGlobalSettingsSetFromPostFactory
     */
    public function getGssFromPostFactory()
    {
        if ($this->gssFromPostFactory === null) {
            $this->gssFromPostFactory = $this->container->get('builderius_setting.factory.builderius_global_settings_set_from_post');
        }

        return $this->gssFromPostFactory;
    }

    /**
     * @return BuilderiusTemplateTypesProviderInterface
     */
    protected function getTemplateTypesProvider()
    {
        if ($this->templateTypesProvider === null) {
            $this->templateTypesProvider = $this->container->get('builderius_template.provider.template_types');
        }

        return $this->templateTypesProvider;
    }

    /**
     * @return BuilderiusSettingsRegistryInterface
     */
    protected function getSettingsRegistry()
    {
        if ($this->settingsRegistry === null) {
            $this->settingsRegistry = $this->container->get('builderius_setting.registry.settings');
        }

        return $this->settingsRegistry;
    }

    /**
     * @return BuilderiusTemplateContentProviderInterface
     */
    protected function getContentProvider()
    {
        if ($this->contentProvider === null) {
            $this->contentProvider = $this->container->get('builderius_template.provider.template_content.composite');
        }

        return $this->contentProvider;
    }
    /**
     * @inheritDoc
     */
    public function up(\wpdb $wpdb)
    {
        $allGss = $this->getGlobalSettingsSet();
        if (null !== $allGss) {
            if ($allGss->getName() !== 'html') {
                $allGssPost = get_post($allGss->getId());
                $allGssPost->post_name = 'html';
                $allGssPost->post_title = 'Global Settings';
                remove_all_filters('content_save_pre');
                wp_update_post($allGssPost);
            }
        }
        foreach ($this->getTemplateTypesProvider()->getTypes() as $type) {
            $typeName = $type->getName();
            $typeGss = $this->getGlobalSettingsSet($typeName);
            if (null !== $typeGss) {
                $typeTemplates = $this->getTemplates($typeName);
                if (!empty($typeTemplates)) {
                    $gssCommit = $typeGss->getActiveBranch()->getActiveCommit();
                    if ($gssCommit) {
                        $gssConfig = $gssCommit->getContentConfig();
                        $gssSettings = [];
                        if (isset($gssConfig['template']) && isset($gssConfig['template']['settings'])) {
                            $gssSettings = $gssConfig['template']['settings'];
                        }
                        if (!empty($gssSettings)) {
                            foreach ($typeTemplates as $typeTemplate) {
                                $commit = $typeTemplate->getActiveBranch()->getActiveCommit();
                                if ($commit) {
                                    $templateConfig = $commit->getContentConfig();
                                    if (!isset($templateConfig['template'])) {
                                        $templateConfig['template'] = [];
                                        $templateConfig['template']['settings'] = $gssSettings;
                                    } else {
                                        $templateSettings = $templateConfig['template']['settings'];
                                        foreach ($gssSettings as $gssSetting) {
                                            $exists = false;
                                            $index = null;
                                            foreach ($templateSettings as $k => $templateSetting) {
                                                if ($gssSetting['name'] === $templateSetting['name']) {
                                                    $exists = true;
                                                    $index = $k;
                                                    break;
                                                }
                                            }
                                            if (false === $exists) {
                                                $templateConfig['template']['settings'][] = $gssSetting;
                                            } else {
                                                $setting = $this->getSettingsRegistry()->getSetting($typeName, 'html', $templateSettings[$index]['name']);
                                                if ($templateSettings[$index]['name'] === 'cssVars') {
                                                    $templateConfig['template']['settings'][$index]['value'] =
                                                        BuilderiusGlobalTypeSettingsTransformationBeforeReleaseImportListener::processCssArraySetting(
                                                            'a2',
                                                            $templateSettings[$index]['value'],
                                                            $gssSetting['value']
                                                        );
                                                } elseif ($setting instanceof BuilderiusSettingCssAwareInterface) {
                                                    $finalValue = $gssSetting;
                                                    foreach ($templateSettings[$index]['value'] as $mediaQuery => $pseudoClassData) {
                                                        foreach ($pseudoClassData as $pseudoClass => $value) {
                                                            $finalValue[$mediaQuery][$pseudoClass] = $value;
                                                        }
                                                    }
                                                    $templateConfig['template']['settings'][$index]['value'] = $finalValue;
                                                } elseif (in_array($templateSettings[$index]['name'], ['dataVars', 'jsLibraries', 'cssLibraries'])) {
                                                    $templateConfig['template']['settings'][$index]['value'] =
                                                        BuilderiusGlobalTypeSettingsTransformationBeforeReleaseImportListener::processNonCssArraySetting(
                                                            'b1',
                                                            $templateSettings[$index]['value'],
                                                            $gssSetting['value']
                                                        );
                                                } elseif (in_array($templateSettings[$index]['name'], ['htmlAttribute', 'customJs', 'customCss', 'stringTranslations'])) {
                                                    $templateConfig['template']['settings'][$index]['value'] =
                                                        BuilderiusGlobalTypeSettingsTransformationBeforeReleaseImportListener::processNonCssArraySetting(
                                                            'a1',
                                                            $templateSettings[$index]['value'],
                                                            $gssSetting['value']
                                                        );
                                                }
                                            }
                                        }
                                    }
                                    $content = json_encode($this->getContentProvider()->getContent(
                                        'html',
                                        $templateConfig
                                    ));
                                    $post = get_post($commit->getId());
                                    $post->post_content = $content;
                                    remove_all_filters('content_save_pre');
                                    wp_update_post($post);
                                    update_post_meta(
                                        $post->ID,
                                        BuilderiusCommit::CONTENT_CONFIG_FIELD,
                                        wp_slash(json_encode($templateConfig))
                                    );
                                }
                            }
                        }
                    }
                }
                wp_delete_post($typeGss->getId(), true);
            }
        }
    }

    /**
     * @return BuilderiusGlobalSettingsSetInterface|null
     */
    protected function getGlobalSettingsSet($type = 'all')
    {
        $queryArgs = [
            'post_type' => BuilderiusGlobalSettingsSetPostType::POST_TYPE,
            'post_status' => get_post_stati(),
            'name' => 'html_' . $type,
            'posts_per_page' => -1,
            'no_found_rows' => true,
        ];
        $posts = $this->getWpQuery()->query($queryArgs);
        if (empty($posts)) {
            return null;
        } else {
            if ($type === 'all') {
                $queryArgs2 = [
                    'post_type' => BuilderiusGlobalSettingsSetPostType::POST_TYPE,
                    'post_status' => get_post_stati(),
                    'name' => 'html',
                    'posts_per_page' => -1,
                    'no_found_rows' => true,
                ];
                $wposts = $this->getWpQuery()->query($queryArgs2);
                if (!empty($wposts)) {
                    foreach ($wposts as $p) {
                        wp_delete_post($p->ID, true);
                    }
                }
            }

            return $this->getGssFromPostFactory()->createGlobalSettingsSet($posts[0]);
        }
    }
    /**
     * @param string $type
     * @return BuilderiusTemplate[]
     */
    protected function getTemplates($type)
    {
        $queryArgs = [
            'post_type' => BuilderiusTemplatePostType::POST_TYPE,
            'post_status' => get_post_stati(),
            'posts_per_page' => -1,
            'no_found_rows' => true,
            'tax_query' => [
                [
                    'taxonomy' => BuilderiusTemplateTechnologyTaxonomy::NAME,
                    'field' => 'slug',
                    'include_children' => false,
                    'terms' => ['html']
                ],
                [
                    'taxonomy' => BuilderiusTemplateTypeTaxonomy::NAME,
                    'field' => 'slug',
                    'include_children' => false,
                    'terms' => [$type]
                ]
            ]
        ];
        $posts = $this->getWpQuery()->query($queryArgs);
        $templates = [];
        foreach ($posts as $post) {
            $templates[] = $this->getTemplateFromPostFactory()->createTemplate($post);
        }

        return $templates;
    }
}