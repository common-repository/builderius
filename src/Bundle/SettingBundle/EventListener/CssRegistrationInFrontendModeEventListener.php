<?php

namespace Builderius\Bundle\SettingBundle\EventListener;

use Builderius\Bundle\DeliverableBundle\Provider\BuilderiusDeliverableProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateCssContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModuleProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModulesProviderInterface;
use Builderius\Bundle\TemplateBundle\Sanitizer\CssContentSanitizer;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\InlineAssetsContainingEvent;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetDataItem;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAsset;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\ConditionAwareEventListener;
use Builderius\Symfony\Component\Templating\EngineInterface;

class CssRegistrationInFrontendModeEventListener extends ConditionAwareEventListener
{
    /**
     * @var BuilderiusDeliverableProviderInterface
     */
    private $deliverableProvider;

    /**
     * @var DeliverableTemplateSubModuleProviderInterface
     */
    private $dtsmProvider;

    /**
     * @var DeliverableTemplateSubModulesProviderInterface
     */
    private $dhtsmsProvider;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @param BuilderiusDeliverableProviderInterface $deliverableProvider
     * @param DeliverableTemplateSubModuleProviderInterface $dtsmProvider
     * @param DeliverableTemplateSubModulesProviderInterface $dhtsmsProvider
     * @param EngineInterface $templatingEngine
     */
    public function __construct(
        BuilderiusDeliverableProviderInterface $deliverableProvider,
        DeliverableTemplateSubModuleProviderInterface $dtsmProvider,
        DeliverableTemplateSubModulesProviderInterface $dhtsmsProvider,
        EngineInterface $templatingEngine
    ) {
        $this->deliverableProvider = $deliverableProvider;
        $this->dtsmProvider = $dtsmProvider;
        $this->dhtsmsProvider = $dhtsmsProvider;
        $this->templatingEngine = $templatingEngine;
    }

    /**
     * @param InlineAssetsContainingEvent $event
     * @throws \Exception
     */
    public function beforeAssetsRegistration(InlineAssetsContainingEvent $event)
    {
        $assets = $event->getAssets();
        $templateSubModule = $this->dtsmProvider->getTemplateSubModule();
        if (!$templateSubModule) {
            $hookTemplateSubModules = $this->dhtsmsProvider->getTemplateSubModules();
            if (!empty($hookTemplateSubModules)) {
                $templateSubModule = reset($hookTemplateSubModules);
            }
        }
        if ($templateSubModule) {
            $technology = $templateSubModule->getTechnology();
            $deliverable = $this->deliverableProvider->getDeliverable();
            $gssAll = $deliverable->getSubModules('global_settings_set', $technology);
            if (!empty($gssAll)) {
                $gssAll = reset($gssAll);
                if ($cssContent = $gssAll->getContent(BuilderiusTemplateCssContentProvider::CONTENT_TYPE)) {
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
        $event->setAssets($assets);
    }
}