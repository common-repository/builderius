<?php

namespace Builderius\Bundle\SettingBundle\EventListener;

use Builderius\Bundle\SettingBundle\Factory\BuilderiusGlobalSettingsSetFromPostFactory;
use Builderius\Bundle\SettingBundle\Registration\BuilderiusGlobalSettingsSetPostType;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateCssContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplatesProviderInterface;
use Builderius\Bundle\TemplateBundle\Sanitizer\CssContentSanitizer;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\InlineAssetsContainingEvent;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetDataItem;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAsset;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\ConditionAwareEventListener;
use Builderius\Symfony\Component\Templating\EngineInterface;

class CssRegistrationInPreviewModeEventListener extends ConditionAwareEventListener
{
    /**
     * @var BuilderiusTemplateProviderInterface
     */
    private $builderiusTemplateProvider;

    /**
     * @var BuilderiusTemplatesProviderInterface
     */
    private $builderiusHookTemplatesProvider;

    /**
     * @var BuilderiusGlobalSettingsSetFromPostFactory
     */
    protected $globalSettingsSetFromPostFactory;

    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $builderiusCache;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @param BuilderiusTemplateProviderInterface $builderiusTemplateProvider
     * @param BuilderiusTemplatesProviderInterface $builderiusHookTemplatesProvider
     * @param BuilderiusGlobalSettingsSetFromPostFactory $globalSettingsSetFromPostFactory
     * @param \WP_Query $wpQuery
     * @param BuilderiusRuntimeObjectCache $builderiusCache
     * @param EngineInterface $templatingEngine
     */
    public function __construct(
        BuilderiusTemplateProviderInterface $builderiusTemplateProvider,
        BuilderiusTemplatesProviderInterface $builderiusHookTemplatesProvider,
        BuilderiusGlobalSettingsSetFromPostFactory $globalSettingsSetFromPostFactory,
        \WP_Query $wpQuery,
        BuilderiusRuntimeObjectCache $builderiusCache,
        EngineInterface $templatingEngine
    ) {
        $this->builderiusTemplateProvider = $builderiusTemplateProvider;
        $this->builderiusHookTemplatesProvider = $builderiusHookTemplatesProvider;
        $this->globalSettingsSetFromPostFactory = $globalSettingsSetFromPostFactory;
        $this->wpQuery = $wpQuery;
        $this->builderiusCache = $builderiusCache;
        $this->templatingEngine = $templatingEngine;
    }

    /**
     * @param InlineAssetsContainingEvent $event
     * @throws \Exception
     */
    public function beforeAssetsRegistration(InlineAssetsContainingEvent $event)
    {
        $assets = $event->getAssets();
        $template = $this->builderiusTemplateProvider->getTemplate();
        if (!$template) {
            $hookTemplates = $this->builderiusHookTemplatesProvider->getTemplates();
            if (!empty($hookTemplates)) {
                $template = reset($hookTemplates);
            }
        }
        if ($template) {
            $technologyName = $template->getTechnology();
            $posts = $this->builderiusCache->get(sprintf('builderius_gss_posts_%s', $technologyName));
            if (false == $posts) {
                $queryArgs = [
                    'post_type' => BuilderiusGlobalSettingsSetPostType::POST_TYPE,
                    'post_status' => get_post_stati(),
                    'name' => $technologyName,
                    'posts_per_page' => -1,
                    'no_found_rows' => true,
                    'orderby' => 'name',
                    'order' => 'ASC'
                ];
                $posts = $this->wpQuery->query($queryArgs);
                $this->builderiusCache->set(sprintf('builderius_gss_posts_%s', $technologyName), $posts);
            }
            foreach ($posts as $post) {
                $globalSettingsSet = $this->globalSettingsSetFromPostFactory->createGlobalSettingsSet($post);
                $branch = $globalSettingsSet->getActiveBranch();
                if ($branch) {
                    if ($cssContent = $branch->getContent(BuilderiusTemplateCssContentProvider::CONTENT_TYPE)) {
                        $cssContent = $this->templatingEngine->render(
                            'BuilderiusTemplateBundle:templateDynamicContent.twig',
                            [
                                'dynamicContent' => $cssContent
                            ]
                        );
                        $cssContent = CssContentSanitizer::sanitize($cssContent);
                        if (trim($cssContent) !== '') {
                            $asset = new InlineAsset([
                                InlineAsset::TYPE_FIELD => 'style',
                                InlineAsset::CONTENT_FIELD => $cssContent
                            ]);
                            $assetDataItem = new AssetDataItem([
                                AssetDataItem::GROUP_FIELD => 'htmlAttributes',
                                AssetDataItem::KEY_FIELD => 'class',
                                AssetDataItem::VALUE_FIELD => 'builderius-css'
                            ]);
                            $asset->addAssetDataItem($assetDataItem);
                            if (!in_array($asset, $assets)) {
                                $assets[] = $asset;
                            }
                        }
                    } else {
                        $commit = $branch->getActiveCommit();
                        if ($commit) {
                            if ($cssContent = $commit->getContent(BuilderiusTemplateCssContentProvider::CONTENT_TYPE)) {
                                $cssContent = $this->templatingEngine->render(
                                    'BuilderiusTemplateBundle:templateDynamicContent.twig',
                                    [
                                        'dynamicContent' => $cssContent
                                    ]
                                );
                                $cssContent = CssContentSanitizer::sanitize($cssContent);
                                if (trim($cssContent) !== '') {
                                    $asset = new InlineAsset([
                                        InlineAsset::TYPE_FIELD => 'style',
                                        InlineAsset::CONTENT_FIELD => $cssContent
                                    ]);
                                    $assetDataItem = new AssetDataItem([
                                        AssetDataItem::GROUP_FIELD => 'htmlAttributes',
                                        AssetDataItem::KEY_FIELD => 'class',
                                        AssetDataItem::VALUE_FIELD => 'builderius-css'
                                    ]);
                                    $asset->addAssetDataItem($assetDataItem);
                                    if (!in_array($asset, $assets)) {
                                        $assets[] = $asset;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $event->setAssets($assets);
    }
}