<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateCssContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModuleProviderInterface;
use Builderius\Bundle\TemplateBundle\Sanitizer\CssContentSanitizer;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\InlineAssetsContainingEvent;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetDataItem;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAsset;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\ConditionAwareEventListener;
use Builderius\Symfony\Component\Templating\EngineInterface;

class CssRegistrationInFrontendModeEventListener extends ConditionAwareEventListener
{
    /**
     * @var DeliverableTemplateSubModuleProviderInterface
     */
    private $dtsmProvider;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @param DeliverableTemplateSubModuleProviderInterface $dtsmProvider
     * @param EngineInterface $templatingEngine
     */
    public function __construct(
        DeliverableTemplateSubModuleProviderInterface $dtsmProvider,
        EngineInterface $templatingEngine
    ) {
        $this->dtsmProvider = $dtsmProvider;
        $this->templatingEngine = $templatingEngine;
    }

    public function beforeAssetsRegistration(InlineAssetsContainingEvent $event)
    {
        $assets = $event->getAssets();
        $templateSubModule = $this->dtsmProvider->getTemplateSubModule();
        if ($templateSubModule) {
            if ($cssContent = $templateSubModule->getContent(BuilderiusTemplateCssContentProvider::CONTENT_TYPE)) {
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
                    $assets[] = $asset;
                }
            }
        }
        $event->setAssets($assets);
    }
}