<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateInterface;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateContentProviderInterface;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranchInterface;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommit;

class TemplateCssSettingsClearOnTemplateTypeChangedToHookEventListener
{
    /**
     * @var BuilderiusTemplateFromPostFactory
     */
    private $templateFromPostFactory;

    /**
     * @var BuilderiusTemplateContentProviderInterface
     */
    private $contentProvider;

    /**
     * @var BuilderiusSettingsRegistryInterface
     */
    private $settingsRegistry;

    /**
     * @param BuilderiusTemplateFromPostFactory $templateFromPostFactory
     * @param BuilderiusTemplateContentProviderInterface $contentProvider
     * @param BuilderiusSettingsRegistryInterface $settingsRegistry
     */
    public function __construct(
        BuilderiusTemplateFromPostFactory $templateFromPostFactory,
        BuilderiusTemplateContentProviderInterface $contentProvider,
        BuilderiusSettingsRegistryInterface $settingsRegistry
    ) {
        $this->templateFromPostFactory = $templateFromPostFactory;
        $this->contentProvider = $contentProvider;
        $this->settingsRegistry = $settingsRegistry;
    }

    /**
     * @param PostContainingEvent $event
     */
    public function clearTemplateCss(PostContainingEvent $event)
    {
        $post = $event->getPost();
        if ($post instanceof \WP_Post && $post->post_type === BuilderiusTemplatePostType::POST_TYPE) {
            $template = $this->templateFromPostFactory->createTemplate($post);
            if ($template instanceof BuilderiusTemplateInterface) {
                $branch = $template->getActiveBranch();
                if ($branch instanceof BuilderiusBranchInterface) {
                    $ncc = $branch->getNotCommittedConfig();
                    if (is_array($ncc) && isset($ncc['template']) && isset($ncc['template']['settings'])) {
                        $config = $this->filterSettings($ncc);
                        $content = $this->contentProvider->getContent('html', $config);
                        $branchPost = get_post($branch->getId());
                        $branchPost->post_content = json_encode($content, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
                        remove_all_filters('content_save_pre');
                        wp_update_post( $branchPost);
                    }
                    foreach ($branch->getCommits() as $commit) {
                        $config = $commit->getContentConfig();
                        if (is_array($config) && isset($config['template']) && isset($config['template']['settings'])) {
                            $config = $this->filterSettings($config);
                            $content = $this->contentProvider->getContent('html', $config);
                            $commitPost = get_post($commit->getId());
                            $commitPost->post_content = json_encode($content, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
                            remove_all_filters('content_save_pre');
                            wp_update_post($commitPost);
                            update_post_meta(
                                $commit->getId(),
                                BuilderiusCommit::CONTENT_CONFIG_FIELD,
                                wp_slash(json_encode($config, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE))
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $config
     * @return array
     */
    private function filterSettings(array $config)
    {
        $templateType = $config['template']['type'];
        foreach ($config['template']['settings'] as $k => $templateSettingConfig) {
            $templateSetting = $this->settingsRegistry->getSetting(
                $templateType,
                'html',
                $templateSettingConfig['name']
            );
            if ($templateSetting && $templateSetting instanceof BuilderiusSettingCssAwareInterface &&
                $templateSetting->getContentType() === 'css') {
                unset($config['template']['settings'][$k]);
            }
        }
        $config['template']['settings'] = array_values($config['template']['settings']);

        return $config;
    }
}