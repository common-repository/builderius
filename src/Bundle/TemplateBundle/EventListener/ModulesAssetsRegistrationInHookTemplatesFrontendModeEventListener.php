<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\Bundle\ModuleBundle\Model\ModuleAssetWithConditionInterface;
use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateModulesWithAssetsContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModuleProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModulesProviderInterface;
use Builderius\Bundle\TemplateBundle\Twig\TemplateDataVarsExtension;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\AssetsContainingEvent;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetAwareInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\ConditionAwareEventListener;

class ModulesAssetsRegistrationInHookTemplatesFrontendModeEventListener extends AbstractModulesAssetsRegistrationEventListener
{
    /**
     * @var DeliverableTemplateSubModulesProviderInterface
     */
    private $dtsmProvider;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param DeliverableTemplateSubModulesProviderInterface $dtsmProvider
     * @param BuilderiusModulesProviderInterface $modulesProvider
     * @param TemplateDataVarsExtension $twigExtension
     * @param ExpressionLanguage $expressionLanguage
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        DeliverableTemplateSubModulesProviderInterface $dtsmProvider,
        BuilderiusModulesProviderInterface $modulesProvider,
        TemplateDataVarsExtension $twigExtension,
        ExpressionLanguage $expressionLanguage,
        BuilderiusRuntimeObjectCache $cache
    ) {
        $this->dtsmProvider = $dtsmProvider;
        $this->modulesProvider = $modulesProvider;
        $this->twigExtension = $twigExtension;
        $this->expressionLanguage = $expressionLanguage;
        $this->cache = $cache;
    }

    public function beforeAssetsRegistration(AssetsContainingEvent $event)
    {
        $assets = $event->getAssets();
        $templateSubModules = $this->dtsmProvider->getTemplateSubModules();
        if (!empty($templateSubModules)) {
            foreach ($templateSubModules as $templateSubModule) {
                $this->cache->set('builderius_dtsm_hook_template', $templateSubModule);
                $index = $this->cache->get(sprintf('dtsm_hook_modules_assets_registration_%d_index', $templateSubModule->getId()));
                if (false === $index) {
                    $index = 0;
                } else {
                    $index = $index + 1;
                }
                $cachedHookArgs = $this->cache->get(sprintf('dtsm_hook_template_args_%d_%d', $templateSubModule->getId(), $index));
                $this->cache->set(sprintf('dtsm_hook_modules_assets_registration_%d_index', $templateSubModule->getId()), $index);
                if (false !== $cachedHookArgs) {
                    $this->cache->set('hook_template_args', $cachedHookArgs);
                }
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
                $this->cache->delete('builderius_dtsm_hook_template');
            }
        }
        $event->setAssets($this->processSsr($assets));
    }
}