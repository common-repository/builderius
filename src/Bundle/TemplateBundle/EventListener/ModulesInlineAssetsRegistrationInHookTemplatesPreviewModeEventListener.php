<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateModulesWithInlineAssetsContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplatesProviderInterface;
use Builderius\Bundle\TemplateBundle\Twig\TemplateDataVarsExtension;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\AssetsContainingEvent;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\InlineAssetsContainingEvent;
use Builderius\Symfony\Component\Templating\EngineInterface;

class ModulesInlineAssetsRegistrationInHookTemplatesPreviewModeEventListener extends AbstractModulesInlineAssetsRegistrationEventListener
{
    /**
     * @var BuilderiusTemplatesProviderInterface
     */
    private $builderiusHookTemplatesProvider;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @var string
     */
    private $assetType;

    /**
     * @param BuilderiusTemplatesProviderInterface $builderiusHookTemplatesProvider
     * @param BuilderiusModulesProviderInterface $modulesProvider
     * @param TemplateDataVarsExtension $twigExtension
     * @param ExpressionLanguage $expressionLanguage
     * @param BuilderiusRuntimeObjectCache $cache
     * @param EngineInterface $templatingEngine
     * @param string $assetType
     */
    public function __construct(
        BuilderiusTemplatesProviderInterface $builderiusHookTemplatesProvider,
        BuilderiusModulesProviderInterface $modulesProvider,
        TemplateDataVarsExtension $twigExtension,
        ExpressionLanguage $expressionLanguage,
        BuilderiusRuntimeObjectCache $cache,
        EngineInterface $templatingEngine,
        $assetType
    ) {
        $this->builderiusHookTemplatesProvider = $builderiusHookTemplatesProvider;
        $this->modulesProvider = $modulesProvider;
        $this->twigExtension = $twigExtension;
        $this->expressionLanguage = $expressionLanguage;
        $this->cache = $cache;
        $this->templatingEngine = $templatingEngine;
        $this->assetType = $assetType;
    }

    /**
     * @param AssetsContainingEvent $event
     * @return void
     * @throws \Exception
     */
    public function beforeAssetsRegistration(InlineAssetsContainingEvent $event)
    {
        $assets = $event->getAssets();
        $templates = $this->builderiusHookTemplatesProvider->getTemplates();
        if (!empty($templates)) {
            foreach ($templates as $template) {
                $this->cache->set('builderius_hook_template', $template);
                $index = $this->cache->get(sprintf('hook_modules_inline_assets_registration_%d_index', $template->getId()));
                if (false === $index) {
                    $index = 0;
                } else {
                    $index = $index + 1;
                }
                $cachedHookArgs = $this->cache->get(sprintf('hook_template_args_%d_%d', $template->getId(), $index));
                $this->cache->set(sprintf('hook_modules_inline_assets_registration_%d_index', $template->getId()), $index);
                if (false !== $cachedHookArgs) {
                    $this->cache->set('hook_template_args', $cachedHookArgs);
                }
                $typeName = $template->getType();
                $technologyName = $template->getTechnology();
                if ($branch = $template->getActiveBranch()) {
                    if ($modulesAssetsContent = $branch->getContent(BuilderiusTemplateModulesWithInlineAssetsContentProvider::CONTENT_TYPE)) {
                        $config = $branch->getNotCommittedConfig();
                        $this->setConfigVersion($config['version']);
                        if (is_array($modulesAssetsContent) && !empty($modulesAssetsContent)) {
                            foreach ($modulesAssetsContent as $moduleName) {
                                $assets = $this->processModuleAssets($moduleName, $typeName, $technologyName, $this->assetType, $assets);
                            }
                        }
                    } elseif ($commit = $branch->getActiveCommit()) {
                        if ($modulesAssetsContent = $commit->getContent(BuilderiusTemplateModulesWithInlineAssetsContentProvider::CONTENT_TYPE)) {
                            $config = $commit->getContentConfig();
                            $this->setConfigVersion($config['version']);
                            if (is_array($modulesAssetsContent) && !empty($modulesAssetsContent)) {
                                foreach ($modulesAssetsContent as $moduleName) {
                                    $assets = $this->processModuleAssets($moduleName, $typeName, $technologyName, $this->assetType, $assets);
                                }
                            }
                        }
                    }
                }
                $this->cache->delete('builderius_hook_template');
            }
        }
        $event->setAssets($assets);
    }
}