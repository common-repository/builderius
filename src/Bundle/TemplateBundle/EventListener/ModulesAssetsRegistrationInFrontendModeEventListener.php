<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\Bundle\ModuleBundle\Model\ModuleAssetWithConditionInterface;
use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateModulesWithAssetsContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModuleProviderInterface;
use Builderius\Bundle\TemplateBundle\Twig\TemplateDataVarsExtension;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\AssetsContainingEvent;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetAwareInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\ConditionAwareEventListener;

class ModulesAssetsRegistrationInFrontendModeEventListener extends AbstractModulesAssetsRegistrationEventListener
{
    /**
     * @var DeliverableTemplateSubModuleProviderInterface
     */
    private $dtsmProvider;

    /**
     * @param DeliverableTemplateSubModuleProviderInterface $dtsmProvider
     * @param BuilderiusModulesProviderInterface $modulesProvider
     * @param TemplateDataVarsExtension $twigExtension
     * @param ExpressionLanguage $expressionLanguage
     */
    public function __construct(
        DeliverableTemplateSubModuleProviderInterface $dtsmProvider,
        BuilderiusModulesProviderInterface $modulesProvider,
        TemplateDataVarsExtension $twigExtension,
        ExpressionLanguage $expressionLanguage
    ) {
        $this->dtsmProvider = $dtsmProvider;
        $this->modulesProvider = $modulesProvider;
        $this->twigExtension = $twigExtension;
        $this->expressionLanguage = $expressionLanguage;
    }

    public function beforeAssetsRegistration(AssetsContainingEvent $event)
    {
        $assets = $event->getAssets();
        $templateSubModule = $this->dtsmProvider->getTemplateSubModule();
        if ($templateSubModule) {
            if ($modulesAssetsContent = $templateSubModule->getContent(BuilderiusTemplateModulesWithAssetsContentProvider::CONTENT_TYPE)) {
                if (is_array($modulesAssetsContent) && !empty($modulesAssetsContent)) {
                    $config = $templateSubModule->getContentConfig();
                    $this->setConfigVersion($config['version']);
                    $typeName = $templateSubModule->getEntityType();
                    $technologyName = $templateSubModule->getTechnology();
                    foreach ($modulesAssetsContent as $moduleName) {
                        $assets = $this->processModuleAssets($moduleName, $typeName, $technologyName, $assets);
                    }
                }
            }
        }
        $event->setAssets($this->processSsr($assets));
    }
}