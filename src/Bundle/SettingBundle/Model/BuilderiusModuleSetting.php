<?php

namespace Builderius\Bundle\SettingBundle\Model;

use Builderius\Bundle\SettingBundle\Generator\BuilderiusSettingValueKeyGenerator;

class BuilderiusModuleSetting extends AbstractBuilderiusSetting implements BuilderiusModuleSettingInterface
{
    const APPLIED_TO_MODULES_FIELD = 'appliedToModules';
    const APPLIED_TO_ALL_MODULES_FIELD = 'appliedToAllModules';
    const EXCLUDED_FROM_MODULES_FIELD = 'excludedFromModules';

    /**
     * @inheritDoc
     */
    public function setAppliedToModules(array $modules)
    {
        if (!empty($modules)) {
            $this->setAppliedToAllModules(false);
        }
        $this->set(self::APPLIED_TO_MODULES_FIELD, $modules);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addAppliedToModule($module)
    {
        if ($this->isAppliedToAllModules() && in_array($module, $this->getExcludedFromModules())) {
            $excludedModules = $this->getExcludedFromModules();
            foreach ($excludedModules as $k => $exclModule) {
                if ($exclModule === $module) {
                    unset($excludedModules[$k]);
                }
            }
            $this->setExcludedFromModules($excludedModules);

            return $this;
        } elseif (!$this->isAppliedToAllModules() && !in_array($module, $this->getAppliedToModules())) {
            $modules = $this->getAppliedToModules();
            $modules[] = $module;
            $this->setAppliedToModules($modules);

            return $this;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAppliedToModules()
    {
        return $this->get(self::APPLIED_TO_MODULES_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setExcludedFromModules(array $modules)
    {
        if (!empty($modules)) {
            $this->setAppliedToAllModules(true);
        }
        $this->set(self::EXCLUDED_FROM_MODULES_FIELD, $modules);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addExcludedFromModule($module)
    {
        if (!$this->isAppliedToAllModules() && in_array($module, $this->getAppliedToModules())) {
            $appliedModules = $this->getAppliedToModules();
            foreach ($appliedModules as $k => $applModule) {
                if ($applModule === $module) {
                    unset($appliedModules[$k]);
                }
            }
            $this->setAppliedToModules($appliedModules);

            return $this;
        } elseif ($this->isAppliedToAllModules() && !in_array($module, $this->getExcludedFromModules())) {
            $modules = $this->getExcludedFromModules();
            $modules[] = $module;
            $this->setExcludedFromModules($modules);

            return $this;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExcludedFromModules()
    {
        return $this->get(self::EXCLUDED_FROM_MODULES_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function isAppliedToAllModules()
    {
        return (bool)$this->get(self::APPLIED_TO_ALL_MODULES_FIELD, false);
    }

    /**
     * @inheritDoc
     */
    public function setAppliedToAllModules($appliedToAllModules)
    {
        if ($appliedToAllModules === true) {
            $this->setAppliedToModules([]);
        } else {
            $this->setExcludedFromModules([]);
        }
        $this->set(self::APPLIED_TO_ALL_MODULES_FIELD, $appliedToAllModules);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValues($for = null)
    {
        $defaultValuesByModules = $this->get(self::DEFAULT_VALUES_FIELD, []);
        if ($for === null) {
            return isset($defaultValuesByModules['all']) ? $defaultValuesByModules['all'] : [];
        } else {
            return isset($defaultValuesByModules[$for]) ? $defaultValuesByModules[$for] : [];
        }
    }

    /**
     * @inheritDoc
     */
    public function addDefaultValue(BuilderiusSettingValueInterface $value, $for = [])
    {
        apply_filters(self::ADD_VALUE_TAG, $value, $this);
        $vv = $value->getValue();
        $value->setValue($vv);
        $key = BuilderiusSettingValueKeyGenerator::generate($value);
        $defaultValues = $this->get(self::DEFAULT_VALUES_FIELD, []);
        if (empty($for) && $this->isAppliedToAllModules()) {
            $defaultValues['all'][$key] = $value;
        } elseif (empty($for) && !empty($this->getAppliedToModules())) {
            foreach ($this->getAppliedToModules() as $module) {
                $defaultValues[$module][$key] = $value;
            }
        } elseif (!empty($for) && empty($this->getAppliedToModules())) {
            foreach ($for as $module) {
                $defaultValues[$module][$key] = $value;
            }
        } elseif (!empty($for) && !empty($this->getAppliedToModules())) {
            foreach ($for as $module) {
                $defaultValues[$module][$key] = $value;
            }
        }
        $this->set(self::DEFAULT_VALUES_FIELD, $defaultValues);

        return $this;
    }
}