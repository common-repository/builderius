<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\Bundle\ModuleBundle\Model\ModuleAssetWithConditionInterface;
use Builderius\Bundle\ModuleBundle\Model\ModuleConfigVersionsRelatedAssetInterface;
use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\TemplateBundle\Twig\TemplateDataVarsExtension;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetAwareInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\StyleInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\ConditionAwareEventListener;

abstract class AbstractModulesAssetsRegistrationEventListener extends ConditionAwareEventListener
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
     * @param array $assets
     * @return array
     */
    protected function processSsr(array $assets)
    {
        $ssrHandles = [];
        $nonSsrHandles = [];
        foreach ($assets as $asset) {
            $src = $asset->getSource();
            $handle = $asset->getHandle();
            if (strpos($src, 'builderius') !== false && strpos($src, '-ssr.js') !== false) {
                if (!in_array($handle, $ssrHandles)) {
                    $ssrHandles[] = $handle;
                }
            } elseif (strpos($src, 'builderius') !== false && strpos($src, '-ssr.js') === false) {
                if (!in_array($handle, $nonSsrHandles)) {
                    $nonSsrHandles[] = $handle;
                }
            }
        }
        foreach ($ssrHandles as $ssrHandle) {
            $changed = str_replace('-ssr', '', $ssrHandle);
            if (in_array($changed, $nonSsrHandles)) {
                foreach ($assets as $k => $asset) {
                    if ($ssrHandle === $asset->getHandle()) {
                        unset($assets[$k]);
                        break;
                    }
                }
            }
        }

        return $assets;
    }

    /**
     * @param string|array $moduleName
     * @param string $typeName
     * @param string $technologyName
     * @param array $assets
     * @return array
     * @throws \Exception
     */
    protected function processModuleAssets($moduleName, $typeName, $technologyName, array $assets)
    {
        $configVersions = $this->configVersion !== null ? $this->configVersion : [];
        if (is_string($moduleName)) {
            $module = $this->modulesProvider->getModule($moduleName, $typeName, $technologyName, false, $configVersions);
            if ($module && $module instanceof AssetAwareInterface && !empty($module->getAssets())) {
                foreach ($module->getAssets() as $asset) {
                    if (!in_array($asset, $assets)) {
                        $assets[] = $asset;
                    }
                }
            }
        } elseif (is_array($moduleName) && isset($moduleName['name']) && isset($moduleName['visibilityCondition'])) {
            $condResult = $this->twigExtension->evaluateVisibilityCondition($moduleName['visibilityCondition']);
            if (true === $condResult) {
                $module = $this->modulesProvider->getModule($moduleName['name'], $typeName, $technologyName, false, $configVersions);
                if ($module && $module instanceof AssetAwareInterface && !empty($module->getAssets())) {
                    $moduleAssets = [];
                    foreach ($module->getAssets() as $asset) {
                        if ($asset instanceof ModuleAssetWithConditionInterface) {
                            $moduleAssets = $this->processModuleAssetWithCondition($asset, $moduleName, $moduleAssets);
                        } else {
                            if (!in_array($asset, $moduleAssets)) {
                                $moduleAssets[] = $asset;
                            }
                        }
                    }
                    if (null !== $this->configVersion) {
                        $moduleAssets = $this->processVersionRelatedModuleAssets($moduleAssets, $this->configVersion);
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
     * @param ModuleAssetWithConditionInterface $asset
     * @param AssetInterface[] $assets
     * @return AssetInterface[]
     */
    protected function processModuleAssetWithCondition(ModuleAssetWithConditionInterface $asset, array $moduleName, array $assets)
    {
        $context = isset($moduleName['assetConditionContext']) ? $moduleName['assetConditionContext'] : [];
        if (empty($context) && $asset->loadIfEmptyContext()) {
            if (!in_array($asset, $assets)) {
                $assets[] = $asset;
            }
        } else {
            try {
                $result = $this->expressionLanguage->evaluate($asset->getConditionExpression(), $context);
                if (true === $result) {
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
     * @param AssetInterface[] $assets
     * @param array $version
     * @return AssetInterface[]
     */
    protected function processVersionRelatedModuleAssets(array $assets, array $version)
    {
        $filteredAssets = [];
        $scripts = [];
        $styles = [];
        foreach ($assets as $asset) {
            if ($asset instanceof ScriptInterface) {
                if (!isset($scripts[$asset->getHandle()])) {
                    $scripts[$asset->getHandle()] = [];
                }
                $scripts[$asset->getHandle()][] = $asset;
            }
            if ($asset instanceof StyleInterface) {
                if (!isset($styles[$asset->getHandle()])) {
                    $styles[$asset->getHandle()] = [];
                }
                $styles[$asset->getHandle()][] = $asset;
            }
        }
        foreach ($scripts as &$sameHandleAssets) {
            usort(
                $sameHandleAssets,
                function ($a, $b) {
                    if ($a instanceof ModuleConfigVersionsRelatedAssetInterface && !$b instanceof ModuleConfigVersionsRelatedAssetInterface) {
                        return -1;
                    } elseif (!$a instanceof ModuleConfigVersionsRelatedAssetInterface && $b instanceof ModuleConfigVersionsRelatedAssetInterface) {
                        return 1;
                    }

                    return 0;
                });
        }
        foreach ($styles as &$sameHandleAssets) {
            usort(
                $sameHandleAssets,
                function ($a, $b) {
                    if ($a instanceof ModuleConfigVersionsRelatedAssetInterface && !$b instanceof ModuleConfigVersionsRelatedAssetInterface) {
                        return -1;
                    } elseif (!$a instanceof ModuleConfigVersionsRelatedAssetInterface && $b instanceof ModuleConfigVersionsRelatedAssetInterface) {
                        return 1;
                    }

                    return 0;
                });
        }
        foreach ($scripts as $sameHandleScripts) {
            foreach ($sameHandleScripts as $script) {
                $found = false;
                if ($script instanceof ModuleConfigVersionsRelatedAssetInterface) {
                    $aConfVersion = $script->getConfigVersions();
                    foreach ($version as $plugin => $pluginVersion) {
                        if (isset($aConfVersion[$plugin]) && version_compare($version[$plugin], $aConfVersion[$plugin]) < 1) {
                            $filteredAssets[] = $script;
                            $found = true;
                            break;
                        }
                    }
                }
                if (true === $found) {
                    break;
                }
                if (!$script instanceof ModuleConfigVersionsRelatedAssetInterface) {
                    $filteredAssets[] = $script;
                }
            }
        }
        foreach ($styles as $sameHandleStyles) {
            foreach ($sameHandleStyles as $style) {
                $found = false;
                if ($style instanceof ModuleConfigVersionsRelatedAssetInterface) {
                    $aConfVersion = $style->getConfigVersions();
                    foreach ($version as $plugin => $pluginVersion) {
                        if (isset($aConfVersion[$plugin]) && version_compare($version[$plugin], $aConfVersion[$plugin]) < 1) {
                            $filteredAssets[] = $style;
                            $found = true;
                            break;
                        }
                    }
                }
                if (true === $found) {
                    break;
                }
                if (!$style instanceof ModuleConfigVersionsRelatedAssetInterface) {
                    $filteredAssets[] = $style;
                }
            }
        }

        return $filteredAssets;
    }
}