<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateModulesWithInlineAssetsContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModulesProviderInterface;
use Builderius\Bundle\TemplateBundle\Twig\TemplateDataVarsExtension;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\InlineAssetsContainingEvent;
use Builderius\Symfony\Component\Templating\EngineInterface;

class ModulesInlineAssetsRegistrationInHookTemplatesFrontendModeEventListener extends AbstractModulesInlineAssetsRegistrationEventListener
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
     * @var string
     */
    private $assetType;

    /**
     * @param DeliverableTemplateSubModulesProviderInterface $dtsmProvider
     * @param BuilderiusModulesProviderInterface $modulesProvider
     * @param TemplateDataVarsExtension $twigExtension
     * @param ExpressionLanguage $expressionLanguage
     * @param BuilderiusRuntimeObjectCache $cache
     * @param EngineInterface $templatingEngine
     * @param string $assetType
     */
    public function __construct(
        DeliverableTemplateSubModulesProviderInterface $dtsmProvider,
        BuilderiusModulesProviderInterface $modulesProvider,
        TemplateDataVarsExtension $twigExtension,
        ExpressionLanguage $expressionLanguage,
        BuilderiusRuntimeObjectCache $cache,
        EngineInterface $templatingEngine,
        $assetType
    ) {
        $this->dtsmProvider = $dtsmProvider;
        $this->modulesProvider = $modulesProvider;
        $this->twigExtension = $twigExtension;
        $this->expressionLanguage = $expressionLanguage;
        $this->cache = $cache;
        $this->templatingEngine = $templatingEngine;
        $this->assetType = $assetType;
    }

    public function beforeAssetsRegistration(InlineAssetsContainingEvent $event)
    {
        $assets = $event->getAssets();
        $templateSubModules = $this->dtsmProvider->getTemplateSubModules();
        if (!empty($templateSubModules)) {
            foreach ($templateSubModules as $templateSubModule) {
                $this->cache->set('builderius_dtsm_hook_template', $templateSubModule);
                $index = $this->cache->get(sprintf('dtsm_hook_modules_inline_assets_registration_%d_index', $templateSubModule->getId()));
                if (false === $index) {
                    $index = 0;
                } else {
                    $index = $index + 1;
                }
                $cachedHookArgs = $this->cache->get(sprintf('dtsm_hook_template_args_%d_%d', $templateSubModule->getId(), $index));
                $this->cache->set(sprintf('dtsm_hook_modules_inline_assets_registration_%d_index', $templateSubModule->getId()), $index);
                if (false !== $cachedHookArgs) {
                    $this->cache->set('hook_template_args', $cachedHookArgs);
                }
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
                $this->cache->delete('builderius_dtsm_hook_template');
            }
        }
        $event->setAssets($assets);
    }
}