<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateCssContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplatesProviderInterface;
use Builderius\Bundle\TemplateBundle\Sanitizer\CssContentSanitizer;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\InlineAssetsContainingEvent;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetDataItem;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAsset;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\ConditionAwareEventListener;
use Builderius\Symfony\Component\Templating\EngineInterface;

class CssRegistrationInHookTemplatesPreviewModeEventListener extends ConditionAwareEventListener
{
    /**
     * @var BuilderiusTemplatesProviderInterface
     */
    private $builderiusHookTemplatesProvider;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param BuilderiusTemplatesProviderInterface $builderiusHookTemplatesProvider
     * @param EngineInterface $templatingEngine
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        BuilderiusTemplatesProviderInterface $builderiusHookTemplatesProvider,
        EngineInterface $templatingEngine,
        BuilderiusRuntimeObjectCache $cache
    ) {
        $this->builderiusHookTemplatesProvider = $builderiusHookTemplatesProvider;
        $this->templatingEngine = $templatingEngine;
        $this->cache = $cache;
    }

    /**
     * @param InlineAssetsContainingEvent $event
     * @throws \Exception
     */
    public function beforeAssetsRegistration(InlineAssetsContainingEvent $event)
    {
        $assets = $event->getAssets();
        $templates = $this->builderiusHookTemplatesProvider->getTemplates();
        if (!empty($templates)) {
            $cssContent = '';
            foreach ($templates as $template) {
                $this->cache->set('builderius_hook_template', $template);
                $index = $this->cache->get(sprintf('hook_css_registration_%d_index', $template->getId()));
                if (false === $index) {
                    $index = 0;
                } else {
                    $index = $index + 1;
                }
                $cachedHookArgs = $this->cache->get(sprintf('hook_template_args_%d_%d', $template->getId(), $index));
                $this->cache->set(sprintf('hook_css_registration_%d_index', $template->getId()), $index);
                if (false !== $cachedHookArgs) {
                    $this->cache->set('hook_template_args', $cachedHookArgs);
                }
                if ($branch = $template->getActiveBranch()) {
                    if ($cssCnt = $branch->getContent(BuilderiusTemplateCssContentProvider::CONTENT_TYPE)) {
                        $cssCnt = $this->templatingEngine->render(
                            'BuilderiusTemplateBundle:templateDynamicContent.twig',
                            [
                                'dynamicContent' => $cssCnt
                            ]
                        );
                        if ('' === $cssContent) {
                            $cssContent = CssContentSanitizer::sanitize($cssCnt);
                        } else {
                            $cssContent = sprintf("%s\n%s", $cssContent, CssContentSanitizer::sanitize($cssCnt));
                        }
                    } elseif ($commit = $branch->getActiveCommit()) {
                        if ($cssCnt = $commit->getContent(BuilderiusTemplateCssContentProvider::CONTENT_TYPE)) {
                            $cssCnt = $this->templatingEngine->render(
                                'BuilderiusTemplateBundle:templateDynamicContent.twig',
                                [
                                    'dynamicContent' => $cssCnt
                                ]
                            );
                            if ('' === $cssContent) {
                                $cssContent = CssContentSanitizer::sanitize($cssCnt);
                            } else {
                                $cssContent = sprintf("%s\n%s", $cssContent, CssContentSanitizer::sanitize($cssCnt));
                            }
                        }
                    }
                }
                $this->cache->delete('builderius_hook_template');
            }
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
                $assets[] = $asset;
            }
        }
        $event->setAssets($assets);
    }
}