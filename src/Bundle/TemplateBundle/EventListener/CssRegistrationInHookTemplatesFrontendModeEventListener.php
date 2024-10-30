<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateCssContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModulesProviderInterface;
use Builderius\Bundle\TemplateBundle\Sanitizer\CssContentSanitizer;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\InlineAssetsContainingEvent;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetDataItem;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAsset;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\ConditionAwareEventListener;
use Builderius\Symfony\Component\Templating\EngineInterface;

class CssRegistrationInHookTemplatesFrontendModeEventListener extends ConditionAwareEventListener
{
    /**
     * @var DeliverableTemplateSubModulesProviderInterface
     */
    private $dhtsmsProvider;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param DeliverableTemplateSubModulesProviderInterface $dhtsmsProvider
     * @param EngineInterface $templatingEngine
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        DeliverableTemplateSubModulesProviderInterface $dhtsmsProvider,
        EngineInterface $templatingEngine,
        BuilderiusRuntimeObjectCache $cache
    ) {
        $this->dhtsmsProvider = $dhtsmsProvider;
        $this->templatingEngine = $templatingEngine;
        $this->cache = $cache;
    }

    public function beforeAssetsRegistration(InlineAssetsContainingEvent $event)
    {
        $assets = $event->getAssets();
        $templateSubModules = $this->dhtsmsProvider->getTemplateSubModules();
        if (!empty($templateSubModules)) {
            $cssContent = '';
            foreach ($templateSubModules as $templateSubModule) {
                $this->cache->set('builderius_dtsm_hook_template', $templateSubModule);
                $index = $this->cache->get(sprintf('dtsm_hook_css_registration_%d_index', $templateSubModule->getId()));
                if (false === $index) {
                    $index = 0;
                } else {
                    $index = $index + 1;
                }
                $cachedHookArgs = $this->cache->get(sprintf('dtsm_hook_template_args_%d_%d', $templateSubModule->getId(), $index));
                $this->cache->set(sprintf('dtsm_hook_css_registration_%d_index', $templateSubModule->getId()), $index);
                if (false !== $cachedHookArgs) {
                    $this->cache->set('hook_template_args', $cachedHookArgs);
                }
                if ($cssCnt = $templateSubModule->getContent(BuilderiusTemplateCssContentProvider::CONTENT_TYPE)) {
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
                $this->cache->delete('builderius_dtsm_hook_template');
            }
        }
        $event->setAssets($assets);
    }
}