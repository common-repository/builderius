<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\Bundle\ModuleBundle\Model\ModuleInlineAssetWithConditionInterface;
use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\TemplateBundle\Twig\TemplateDataVarsExtension;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetAwareInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\ConditionAwareEventListener;
use Builderius\Symfony\Component\Templating\EngineInterface;

abstract class AbstractModulesInlineAssetsRegistrationEventListener extends ConditionAwareEventListener
{
    /**
     * @var BuilderiusModulesProviderInterface
     */
    protected $modulesProvider;

    /**
     * @var TemplateDataVarsExtension
     */
    protected $twigExtension;

    /**
     * @var ExpressionLanguage
     */
    protected $expressionLanguage;

    /**
     * @var EngineInterface
     */
    protected $templatingEngine;

    /**
     * @var array
     */
    protected $configVersion;

    /**
     * @param array|null $configVersion
     * @return $this
     */
    public function setConfigVersion(array $configVersion = null)
    {
        $this->configVersion = $configVersion;

        return $this;
    }

    /**
     * @param string|array $moduleName
     * @param string $typeName
     * @param string $technologyName
     * @param InlineAssetInterface[] $assets
     * @return array
     * @throws \Exception
     */
    protected function processModuleAssets($moduleName, $typeName, $technologyName, $assetType, array $assets)
    {
        $configVersions = $this->configVersion !== null ? $this->configVersion : [];
        if (is_string($moduleName)) {
            $module = $this->modulesProvider->getModule($moduleName, $typeName, $technologyName, false, $configVersions);
            if ($module && $module instanceof AssetAwareInterface && !empty($module->getInlineAssets())) {
                /** @var InlineAssetInterface $asset */
                foreach ($module->getInlineAssets() as $asset) {
                    if ($assetType === $asset->getType() && !in_array($asset, $assets)) {
                        $assets[] = $asset;
                    }
                }
            }
        } elseif (is_array($moduleName) && isset($moduleName['name']) && isset($moduleName['visibilityCondition'])) {
            $condResult = $this->twigExtension->evaluateVisibilityCondition($moduleName['visibilityCondition']);
            if (true === $condResult) {
                $module = $this->modulesProvider->getModule($moduleName['name'], $typeName, $technologyName, false, $configVersions);
                if ($module && $module instanceof AssetAwareInterface && !empty($module->getInlineAssets())) {
                    $moduleAssets = [];
                    /** @var InlineAssetInterface $asset */
                    foreach ($module->getInlineAssets() as $asset) {
                        if ($assetType === $asset->getType()) {
                            if ($asset instanceof ModuleInlineAssetWithConditionInterface) {
                                $moduleAssets = $this->processModuleAssetWithCondition(clone($asset), $moduleName, $moduleAssets);
                            } else {
                                if (!in_array($asset, $moduleAssets)) {
                                    $content = $this->templatingEngine->render(
                                        $asset->getContentTemplate(),
                                        []
                                    );
                                    $asset->setContent($content);
                                    $moduleAssets[] = $asset;
                                }
                            }
                        }
                    }
                    foreach ($moduleAssets as $moduleAsset) {
                        if (!in_array($moduleAsset, $assets)) {
                            $assets[] = $moduleAsset;
                        }
                    }
                }
            }
        }

        return $assets;
    }

    /**
     * @param ModuleInlineAssetWithConditionInterface $asset
     * @param AssetInterface[] $assets
     * @return AssetInterface[]
     */
    protected function processModuleAssetWithCondition(ModuleInlineAssetWithConditionInterface $asset, array $moduleName, array $assets)
    {
        $context = isset($moduleName['assetConditionContext']) ? $moduleName['assetConditionContext'] : [];
        if (empty($context) && $asset->loadIfEmptyContext()) {
            $content = $this->templatingEngine->render(
                $asset->getContentTemplate(),
                $this->preprocessContext($context)
            );
            $asset->setContent($content);
            if (!in_array($asset, $assets)) {
                $assets[] = $asset;
            }
        } else {
            try {
                $result = $this->expressionLanguage->evaluate($asset->getConditionExpression(), $context);
                if (true === $result) {
                    $content = $this->templatingEngine->render(
                        $asset->getContentTemplate(),
                        $this->preprocessContext($context)
                    );
                    $asset->setContent($content);
                    if (!in_array($asset, $assets)) {
                        $assets[] = $asset;
                    }
                }
            } catch (\Exception $e) {
                return $assets;
            }
        }

        return $assets;
    }

    /**
     * @param array $context
     * @return array
     */
    private function preprocessContext(array $context)
    {
        if (isset($context['htmlAttribute'])) {
            $htmlAttribute = [];
            foreach ($context['htmlAttribute'] as $v) {
                $htmlAttribute[str_replace('-', '_', $v['name'])] = $v['value'];
            }
            $context['htmlAttribute'] = $htmlAttribute;
        }

        return $context;
    }
}