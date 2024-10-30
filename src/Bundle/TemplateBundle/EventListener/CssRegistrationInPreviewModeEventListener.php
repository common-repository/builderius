<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateCssContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;
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
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @param BuilderiusTemplateProviderInterface $builderiusTemplateProvider
     * @param EngineInterface $templatingEngine
     */
    public function __construct(
        BuilderiusTemplateProviderInterface $builderiusTemplateProvider,
        EngineInterface $templatingEngine
    ) {
        $this->builderiusTemplateProvider = $builderiusTemplateProvider;
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
        if ($template) {
            if ($branch = $template->getActiveBranch()) {
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
                        $assets[] = $asset;
                    }
                } elseif ($commit = $branch->getActiveCommit()) {
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
                            $assets[] = $asset;
                        }
                    }
                }
            }
        }
        $event->setAssets($assets);
    }
}