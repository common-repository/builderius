<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateModulesWithInlineAssetsContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModuleProviderInterface;
use Builderius\Bundle\TemplateBundle\Twig\TemplateDataVarsExtension;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\InlineAssetsContainingEvent;
use Builderius\Symfony\Component\Templating\EngineInterface;

class ModulesInlineAssetsRegistrationInFrontendModeEventListener extends AbstractModulesInlineAssetsRegistrationEventListener
{
    /**
     * @var DeliverableTemplateSubModuleProviderInterface
     */
    private $dtsmProvider;

    /**
     * @var string
     */
    private $assetType;

    /**
     * @param DeliverableTemplateSubModuleProviderInterface $dtsmProvider
     * @param BuilderiusModulesProviderInterface $modulesProvider
     * @param TemplateDataVarsExtension $twigExtension
     * @param ExpressionLanguage $expressionLanguage
     * @param EngineInterface $templatingEngine
     * @param string $assetType
     */
    public function __construct(
        DeliverableTemplateSubModuleProviderInterface $dtsmProvider,
        BuilderiusModulesProviderInterface $modulesProvider,
        TemplateDataVarsExtension $twigExtension,
        ExpressionLanguage $expressionLanguage,
        EngineInterface $templatingEngine,
        $assetType
    ) {
        $this->dtsmProvider = $dtsmProvider;
        $this->modulesProvider = $modulesProvider;
        $this->twigExtension = $twigExtension;
        $this->expressionLanguage = $expressionLanguage;
        $this->templatingEngine = $templatingEngine;
        $this->assetType = $assetType;
    }

    /**
     * @param InlineAssetsContainingEvent $event
     * @return void
     * @throws \Exception
     */
    public function beforeAssetsRegistration(InlineAssetsContainingEvent $event)
    {
        $assets = $event->getAssets();
        $templateSubModule = $this->dtsmProvider->getTemplateSubModule();
        if ($templateSubModule) {
            if ($modulesAssetsContent = $templateSubModule->getContent(BuilderiusTemplateModulesWithInlineAssetsContentProvider::CONTENT_TYPE)) {
                if (is_array($modulesAssetsContent) && !empty($modulesAssetsContent)) {
                    $config = $templateSubModule->getContentConfig();
                    $this->setConfigVersion($config['version']);
                    $typeName = $templateSubModule->getEntityType();
                    $technologyName = $templateSubModule->getTechnology();
                    foreach ($modulesAssetsContent as $moduleName) {
                        $assets = $this->processModuleAssets($moduleName, $typeName, $technologyName, $this->assetType, $assets);
                    }
                }
            }
        }
        $event->setAssets($assets);
    }
}