<?php

namespace Builderius\Bundle\SettingBundle\Converter\Setting\ToArray\Chain\Element;

use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusModuleSettingInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;

class BuilderiusModuleSettingToArrayConverterChainElement extends BuilderiusNonModuleSettingToArrayConverterChainElement
{
    /**
     * @var BuilderiusModulesProviderInterface
     */
    private $modulesProvider;

    /**
     * @param BuilderiusModulesProviderInterface $modulesProvider
     * @return $this
     */
    public function setModulesProvider(BuilderiusModulesProviderInterface $modulesProvider)
    {
        $this->modulesProvider = $modulesProvider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isApplicable(
        BuilderiusSettingInterface $setting,
        $formName = 'all',
        $templateType = 'all',
        $templateTechnology = 'all'
    ){
        return $setting instanceof BuilderiusModuleSettingInterface;
    }

    /**
     * @inheritDoc
     */
    public function convertSetting(
        BuilderiusSettingInterface $setting,
        $formName = 'all',
        $templateType = 'all',
        $templateTechnology = 'all'
    ){
        $data = parent::convertSetting($setting);
        $allModules = [];
        $modules = $this->modulesProvider->getModules($templateType, $templateTechnology, false);
        if ($setting->isAppliedToAllModules()) {
            $allModules = array_keys($modules);
            if (!empty($setting->getExcludedFromModules())) {
                foreach ($setting->getExcludedFromModules() as $exclModule) {
                    if (($key = array_search($exclModule, $allModules)) !== false) {
                        unset($allModules[$key]);
                    }
                }
            }
        } elseif (!empty($setting->getAppliedToModules())) {
            $allModules = $setting->getAppliedToModules();
        }
        foreach ($allModules as $moduleName) {
            if (!isset($modules[$moduleName])) {
                if (($key = array_search($moduleName, $allModules)) !== false) {
                    unset($allModules[$key]);
                }
            } else {
                $module = $modules[$moduleName];
                if ($setting instanceof BuilderiusSettingCssAwareInterface && $module->isAllCssSettingsExcluded()) {
                    if (($key = array_search($module->getName(), $allModules)) !== false) {
                        unset($allModules[$key]);
                    }
                }
                if (in_array($setting->getName(), $module->getExcludedSettings())) {
                    if (($key = array_search($module->getName(), $allModules)) !== false) {
                        unset($allModules[$key]);
                    }
                }
            }
        }

        $data['applyTo'] = count($allModules) === count($modules) ? [] : array_values($allModules);

        return $data;
    }
}
