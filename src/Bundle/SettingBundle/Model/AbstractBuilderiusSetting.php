<?php

namespace Builderius\Bundle\SettingBundle\Model;

use Builderius\Bundle\SettingBundle\Generator\BuilderiusSettingValueKeyGenerator;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

abstract class AbstractBuilderiusSetting extends ParameterBag implements BuilderiusSettingInterface
{
    const NAME_FIELD = 'name';
    const LABEL_FIELD = 'label';
    const CONTENT_TYPE_FIELD = 'contentType';
    const DISPLAY_LABEL_FIELD = 'displayLabel';
    const DYNAMIC_DATA_FIELD = 'dynamicData';
    const DYNAMIC_DATA_TYPES_FIELD = 'dynamicDataTypes';
    const PATHS_FIELD = 'paths';
    const SORT_ORDER_FIELD = 'sortOrder';
    const SETTING_COMPONENT_FIELD = 'component';
    const OPTIONS_FIELD = 'options';
    const VALUES_FIELD = 'values';
    const DEFAULT_VALUES_FIELD = 'defaultValues';
    const VALUE_SCHEMA_FIELD = 'schema';
    const VALUE_EXPRESSIONS_FIELD = 'valueExpressions';
    const SHOW_IF_FIELD = 'showIf';
    const FACADES_FIELD = 'facades';
    const APPLIED_TO_TEMPLATE_TYPES = 'appliedToTemplateTypes';
    const APPLIED_TO_ALL_TEMPLATE_TYPES = 'appliedToAllTemplateTypes';
    const EXCLUDED_FROM_TEMPLATE_TYPES = 'excludedFromTemplateTypes';
    const APPLIED_TO_TECHNOLOGIES = 'appliedToTechnologies';
    const APPLIED_TO_ALL_TECHNOLOGIES = 'appliedToAllTechnologies';
    const EXCLUDED_FROM_TECHNOLOGIES = 'excludedFromTechnologies';

    const ADD_VALUE_TAG = 'builderius_setting_add_value';

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->set(self::NAME_FIELD, $name);
        
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return __($this->get(self::LABEL_FIELD), 'builderius');
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        $this->set(self::LABEL_FIELD, $label);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContentType()
    {
        return $this->get(self::CONTENT_TYPE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setContentType($contentType)
    {
        $this->set(self::CONTENT_TYPE_FIELD, $contentType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isDynamicDataAllowed()
    {
        return (bool)$this->get(self::DYNAMIC_DATA_FIELD, false);
    }

    /**
     * @inheritDoc
     */
    public function setDynamicDataAllowed($dynamicData)
    {
        $this->set(self::DYNAMIC_DATA_FIELD, $dynamicData);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDynamicDataTypes()
    {
        return $this->get(self::DYNAMIC_DATA_TYPES_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setDynamicDataTypes(array $dynamicDataTypes)
    {
        $this->set(self::DYNAMIC_DATA_TYPES_FIELD, $dynamicDataTypes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isDisplayLabel()
    {
        return (bool)$this->get(self::DISPLAY_LABEL_FIELD, true);
    }

    /**
     * @inheritDoc
     */
    public function setDisplayLabel($displayLabel)
    {
        $this->set(self::DISPLAY_LABEL_FIELD, $displayLabel);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPaths()
    {
        return $this->get(self::PATHS_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setPaths(array $paths)
    {
        $this->set(self::PATHS_FIELD, []);
        foreach ($paths as $path) {
            $this->addPath($path);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addPath(BuilderiusSettingPathInterface $path)
    {
        $key = sprintf(
            '%s.%s.%s',
            $path->getForm()->getName(),
            $path->getTab()->getName(),
            $path->getCategory()->getName()
        );
        $existingPaths = $this->getPaths();
        $existingPaths[$key] = $path;
        $this->set(self::PATHS_FIELD, $existingPaths);

        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->get(self::SORT_ORDER_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder($sortOrder)
    {
        $this->set(self::SORT_ORDER_FIELD, $sortOrder);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSettingComponent()
    {
        return $this->get(self::SETTING_COMPONENT_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setSettingComponent($settingComponent)
    {
        $this->set(self::SETTING_COMPONENT_FIELD, $settingComponent);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOptions()
    {
        return $this->get(self::OPTIONS_FIELD, []);
    }
    
    /**
     * @inheritDoc
     */
    public function setOptions(array $options)
    {
        $this->set(self::OPTIONS_FIELD, $options);
        
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValues(BuilderiusSettingValuesCollectionInterface $values)
    {
        $this->resetValues();
        $this->addValues($values);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addValues(BuilderiusSettingValuesCollectionInterface $values)
    {
        foreach ($values->toArray() as $value) {
            $this->addValue($value);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValues()
    {
        return $this->get(self::VALUES_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValues($for = null)
    {
        $defaultValuesByPaths = $this->get(self::DEFAULT_VALUES_FIELD, []);
        if ($for === null) {
            return isset($defaultValuesByPaths['all_all_all']) ? $defaultValuesByPaths['all_all_all'] : [];
        } else {
            $forArr = explode('_', $for);
            $defaultValues = isset($defaultValuesByPaths[$for]) ? $defaultValuesByPaths[$for] : [];
            if(isset($defaultValuesByPaths[sprintf('all_%s_%s', $forArr[1], $forArr[2])])) {
                foreach ($defaultValuesByPaths[sprintf('all_%s_%s', $forArr[1], $forArr[2])] as $key => $val) {
                    $defaultValues[$key] = $val;
                }
            }
            if(isset($defaultValuesByPaths[sprintf('all_all_%s', $forArr[2])])) {
                foreach ($defaultValuesByPaths[sprintf('all_all_%s', $forArr[2])] as $key => $val) {
                    $defaultValues[$key] = $val;
                }
            }
            if(isset($defaultValuesByPaths['all_all_all'])) {
                foreach ($defaultValuesByPaths['all_all_all'] as $key => $val) {
                    $defaultValues[$key] = $val;
                }
            }
            if(isset($defaultValuesByPaths[sprintf('%s_%s_all', $forArr[0], $forArr[1])])) {
                foreach ($defaultValuesByPaths[sprintf('%s_%s_all', $forArr[0], $forArr[1])] as $key => $val) {
                    $defaultValues[$key] = $val;
                }
            }
            if(isset($defaultValuesByPaths[sprintf('%s_all_all', $forArr[0])])) {
                foreach ($defaultValuesByPaths[sprintf('%s_all_all', $forArr[0])] as $key => $val) {
                    $defaultValues[$key] = $val;
                }
            }
            if(isset($defaultValuesByPaths[sprintf('all_%s_all', $forArr[1])])) {
                foreach ($defaultValuesByPaths[sprintf('all_%s_all', $forArr[1])] as $key => $val) {
                    $defaultValues[$key] = $val;
                }
            }

            return $defaultValues;
        }
    }

    /**
     * @inheritDoc
     */
    public function resetValues()
    {
        $this->set(self::VALUES_FIELD, []);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addValue(BuilderiusSettingValueInterface $value)
    {
        apply_filters(self::ADD_VALUE_TAG, $value, $this);
        $vv = $value->getValue();
        $schema = $this->getValueSchema();
        foreach (array_keys($schema) as $param) {
            if (!isset($vv[$param])) {
                $vv[$param] = null;
            } elseif (
                $schema[$param]['type'] === 'array' && is_array($vv[$param]) &&
                isset($schema[$param]['of']) && isset($schema[$param]['of']) &&
                isset($schema[$param]['of']['type']) && $schema[$param]['of']['type'] = 'object' &&
                    isset($schema[$param]['of']['shape']) && is_array($schema[$param]['of']['shape'])
            ) {
                foreach ($vv[$param] as $k => $item) {
                    foreach (array_keys($schema[$param]['of']['shape']) as $subParam) {
                        if (!isset($vv[$param][$k][$subParam])) {
                            $vv[$param][$k][$subParam] = null;
                        }
                    }
                }
            }
        }
        $value->setValue($vv);
        $key = BuilderiusSettingValueKeyGenerator::generate($value);
        $values = $this->get(self::VALUES_FIELD, []);
        $values[$key] = $value;
        $this->set(self::VALUES_FIELD, $values);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addDefaultValues(BuilderiusSettingValuesCollectionInterface $values, $for = [])
    {
        foreach ($values->toArray() as $value) {
            $this->addDefaultValue($value, $for);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addDefaultValue(BuilderiusSettingValueInterface $value, $for = [])
    {
        apply_filters(self::ADD_VALUE_TAG, $value, $this);
        $vv = $value->getValue();
        $schema = $this->getValueSchema();
        foreach (array_keys($schema) as $param) {
            if (!isset($vv[$param])) {
                $vv[$param] = null;
            }
        }
        $value->setValue($vv);
        $key = BuilderiusSettingValueKeyGenerator::generate($value);
        $defaultValues = $this->get(self::DEFAULT_VALUES_FIELD, []);
        if (empty($for)) {
            $defaultValues['all_all_all'][$key] = $value;
        } else {
            foreach ($for as $f) {
                $defaultValues[$f][$key] = $value;
            }
        }

        $this->set(self::DEFAULT_VALUES_FIELD, $defaultValues);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValueSchema()
    {
        return $this->get(self::VALUE_SCHEMA_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setValueSchema($valueSchema)
    {
        $this->set(self::VALUE_SCHEMA_FIELD, $valueSchema);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValueExpressions()
    {
        return $this->get(self::VALUE_EXPRESSIONS_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setValueExpressions(array $valueExpressions)
    {
        $this->set(self::VALUE_EXPRESSIONS_FIELD, $valueExpressions);

        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function addValueExpression(BuilderiusSettingValueExpressionInterface $valueExpression)
    {
        $valueExpressions = $this->getValueExpressions();
        $valueExpressions[] = $valueExpression;
        $this->set(self::VALUE_EXPRESSIONS_FIELD, $valueExpressions);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShowIf()
    {
        return $this->get(self::SHOW_IF_FIELD, true);
    }

    /**
     * @inheritDoc
     */
    public function setShowIf($showIf)
    {
        $this->set(self::SHOW_IF_FIELD, $showIf);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFacades()
    {
        return $this->get(self::FACADES_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function addFacade(BuilderiusSettingFacadeInterface $facade)
    {
        $facade->setSetting($this);
        $facades = $this->getFacades();
        $facades[$facade->getName()] = $facade;
        $this->set(self::FACADES_FIELD, $facades);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAppliedToTemplateTypes(array $templateTypes)
    {
        $this->set(self::APPLIED_TO_TEMPLATE_TYPES, $templateTypes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addAppliedToTemplateType($templateType)
    {
        $templateTypes = $this->getAppliedToTemplateTypes();
        $templateTypes[$templateType] = $templateType;
        $this->set(self::APPLIED_TO_TEMPLATE_TYPES, $templateTypes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAppliedToTemplateTypes()
    {
        return $this->get(self::APPLIED_TO_TEMPLATE_TYPES, []);
    }

    /**
     * @param array $templateTypes
     * @return $this
     */
    public function setExcludedFromTemplateTypes(array $templateTypes)
    {
        $this->set(self::EXCLUDED_FROM_TEMPLATE_TYPES, $templateTypes);

        return $this;
    }

    /**
     * @param string $templateType
     * @return $this
     */
    public function addExcludedFromTemplateType($templateType)
    {
        $templateTypes = $this->getExcludedFromTemplateTypes();
        $templateTypes[$templateType] = $templateType;
        $this->set(self::EXCLUDED_FROM_TEMPLATE_TYPES, $templateTypes);

        return $this;
    }

    /**
     * @return array
     */
    public function getExcludedFromTemplateTypes()
    {
        return $this->get(self::EXCLUDED_FROM_TEMPLATE_TYPES, []);
    }

    /**
     * @return bool
     */
    public function isAppliedToAllTemplateTypes()
    {
        return $this->get(self::APPLIED_TO_ALL_TEMPLATE_TYPES, false);
    }

    /**
     * @param bool $appiedToAllTemplateTypes
     * @return $this
     */
    public function setAppliedToAllTemplateTypes($appiedToAllTemplateTypes)
    {
        $this->set(self::APPLIED_TO_ALL_TEMPLATE_TYPES, $appiedToAllTemplateTypes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAppliedToTechnologies(array $technologies)
    {
        $this->set(self::APPLIED_TO_TECHNOLOGIES, $technologies);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addAppliedToTechnology($technology)
    {
        $technologies = $this->getAppliedToTechnologies();
        $technologies[$technology] = $technology;
        $this->set(self::APPLIED_TO_TECHNOLOGIES, $technologies);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAppliedToTechnologies()
    {
        return $this->get(self::APPLIED_TO_TECHNOLOGIES, []);
    }

    /**
     * @inheritDoc
     */
    public function setExcludedFromTechnologies(array $technologies)
    {
        $this->set(self::EXCLUDED_FROM_TECHNOLOGIES, $technologies);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addExcludedFromTechnology($technology)
    {
        $technologies = $this->getExcludedFromTechnologies();
        $technologies[$technology] = $technology;
        $this->set(self::EXCLUDED_FROM_TECHNOLOGIES, $technologies);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExcludedFromTechnologies()
    {
        return $this->get(self::EXCLUDED_FROM_TECHNOLOGIES, []);
    }

    /**
     * @inheritDoc
     */
    public function isAppliedToAllTechnologies()
    {
        return $this->get(self::APPLIED_TO_ALL_TECHNOLOGIES, false);
    }

    /**
     * @inheritDoc
     */
    public function setAppliedToAllTechnologies($appiedToAllTechnologies)
    {
        $this->set(self::APPLIED_TO_ALL_TECHNOLOGIES, $appiedToAllTechnologies);

        return $this;
    }
}
