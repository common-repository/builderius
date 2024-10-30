<?php

namespace Builderius\Bundle\ReleaseBundle\Migrations\v0_9_8;

use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableSubModule;
use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableSubModuleInterface;
use Builderius\Bundle\ReleaseBundle\EventListener\BuilderiusGlobalTypeSettingsTransformationBeforeReleaseImportListener;
use Builderius\Bundle\ReleaseBundle\Factory\BuilderiusReleaseFromPostFactory;
use Builderius\Bundle\ReleaseBundle\Model\BuilderiusRelease;
use Builderius\Bundle\ReleaseBundle\Registration\BulderiusReleasePostType;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateContentProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;
use Builderius\MooMoo\Platform\Bundle\MigrationBundle\Migration\Migration;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareTrait;

class BuilderiusReleaseBundleMigration implements Migration, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusReleaseFromPostFactory
     */
    private $releaseFromPostFactory;

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
     * @return BuilderiusReleaseFromPostFactory
     */
    public function getReleaseFromPostFactory()
    {
        if ($this->releaseFromPostFactory === null) {
            $this->releaseFromPostFactory = $this->container->get('builderius_release.factory.builderius_release_from_post');
        }

        return $this->releaseFromPostFactory;
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
        foreach ($this->getReleases() as $release) {
            $allGss = $release->getSubModules('global_settings_set', 'html', 'all');
            if (!empty($allGss)) {
                $allGss = $allGss[0];
                if ($allGss->getName() !== 'Global Settings') {
                    $allGssPost = get_post($allGss->getId());
                    $allGssPost->post_name = 'Global Settings';
                    $allGssPost->post_title = 'Global Settings';
                    remove_all_filters('content_save_pre');
                    wp_update_post($allGssPost);
                }
            }
            foreach ($this->getTemplateTypesProvider()->getTypes() as $type) {
                $typeName = $type->getName();
                /** @var BuilderiusDeliverableSubModuleInterface[] $typeGss */
                $typeGss = $release->getSubModules('global_settings_set', 'html', $typeName);
                if (!empty($typeGss)) {
                    $typeGss = $typeGss[0];
                    $typeTemplates = $release->getSubModules('template', 'html', $typeName);
                    if (!empty($typeTemplates)) {
                        $gssConfig = $typeGss->getContentConfig();
                        $gssSettings = [];
                        if (isset($gssConfig['template']) && isset($gssConfig['template']['settings'])) {
                            $gssSettings = $gssConfig['template']['settings'];
                        }
                        if (!empty($gssSettings)) {
                            foreach ($typeTemplates as $typeTemplate) {
                                $templateConfig = $typeTemplate->getContentConfig();
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
                                ), JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
                                $post = get_post($typeTemplate->getId());
                                $post->post_content = $content;
                                remove_all_filters('content_save_pre');
                                wp_update_post($post);
                                update_post_meta(
                                    $post->ID,
                                    BuilderiusDeliverableSubModule::CONTENT_CONFIG_FIELD,
                                    wp_slash(json_encode($templateConfig, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE))
                                );
                            }
                        }
                    }
                    wp_delete_post($typeGss->getId(), true);
                }
            }
        }
    }

    /**
     * @return BuilderiusRelease[]
     */
    protected function getReleases()
    {
        $queryArgs = [
            'post_type' => BulderiusReleasePostType::POST_TYPE,
            'post_status' => get_post_stati(),
            'posts_per_page' => -1,
            'no_found_rows' => true,
        ];
        $posts = $this->getWpQuery()->query($queryArgs);
        $releases = [];
        foreach ($posts as $post) {
            $releases[] = $this->getReleaseFromPostFactory()->createRelease($post);
        }

        return $releases;
    }
}