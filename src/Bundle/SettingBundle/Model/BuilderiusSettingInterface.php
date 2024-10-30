<?php

namespace Builderius\Bundle\SettingBundle\Model;

interface BuilderiusSettingInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getLabel();
    
    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getContentType();

    /**
     * @param string $contentType
     * @return $this
     */
    public function setContentType($contentType);

    /**
     * @return bool
     */
    public function isDynamicDataAllowed();

    /**
     * @param bool $dynamicData
     * @return $this
     */
    public function setDynamicDataAllowed($dynamicData);

    /**
     * @return array
     */
    public function getDynamicDataTypes();

    /**
     * @param array $dynamicDataTypes
     * @return $this
     */
    public function setDynamicDataTypes(array $dynamicDataTypes);

    /**
     * @return bool
     */
    public function isDisplayLabel();
    
    /**
     * @param bool $displayLabel
     * @return $this
     */
    public function setDisplayLabel($displayLabel);

    /**
     * @return BuilderiusSettingPathInterface[]
     */
    public function getPaths();

    /**
     * @param BuilderiusSettingPathInterface[] $paths
     * @return $this
     */
    public function setPaths(array $paths);
    
    /**
     * @param BuilderiusSettingPathInterface $path
     * @return $this
     */
    public function addPath(BuilderiusSettingPathInterface $path);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * @return string
     */
    public function getSettingComponent();

    /**
     * @param string $settingComponent
     * @return $this
     */
    public function setSettingComponent($settingComponent);
    
    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options);

    /**
     * @param BuilderiusSettingValuesCollectionInterface $values
     * @return $this
     * @throws \Exception
     */
    public function setValues(BuilderiusSettingValuesCollectionInterface $values);

    /**
     * @param BuilderiusSettingValuesCollectionInterface $values
     * @return $this
     * @throws \Exception
     */
    public function addValues(BuilderiusSettingValuesCollectionInterface $values);

    /**
     * @param BuilderiusSettingValueInterface $value
     * @return $this
     * @throws \Exception
     */
    public function addValue(BuilderiusSettingValueInterface $value);

    /**
     * @param BuilderiusSettingValuesCollectionInterface $values
     * @param array $for
     * @return $this
     * @throws \Exception
     */
    public function addDefaultValues(BuilderiusSettingValuesCollectionInterface $values, $for = []);

    /**
     * @param BuilderiusSettingValueInterface $value
     * @param array $for
     * @return $this
     * @throws \Exception
     */
    public function addDefaultValue(BuilderiusSettingValueInterface $value, $for = []);

    /**
     * @return BuilderiusSettingValueInterface[]
     */
    public function getValues();

    /**
     * @param string $for
     * @return BuilderiusSettingValueInterface[]
     */
    public function getDefaultValues($for = null);

    /**
     * @return $this
     */
    public function resetValues();

    /**
     * @return array
     */
    public function getValueSchema();

    /**
     * @param array $valueSchema
     * @return $this
     */
    public function setValueSchema($valueSchema);

    /**
     * @return BuilderiusSettingValueExpressionInterface[]
     */
    public function getValueExpressions();

    /**
     * @param BuilderiusSettingValueExpressionInterface[] $valueExpressions
     * @return $this
     */
    public function setValueExpressions(array $valueExpressions);
    
    /**
     * @param BuilderiusSettingValueExpressionInterface $valueExpression
     * @return $this
     */
    public function addValueExpression(BuilderiusSettingValueExpressionInterface $valueExpression);
    
    /**
     * @return string
     */
    public function getShowIf();

    /**
     * @param string $showIf
     * @return $this
     */
    public function setShowIf($showIf);

    /**
     * @return BuilderiusSettingFacadeInterface[]
     */
    public function getFacades();

    /**
     * @param BuilderiusSettingFacadeInterface $facade
     * @return $this
     * @throws \Exception
     */
    public function addFacade(BuilderiusSettingFacadeInterface $facade);

    /**
     * @param array $templateTypes
     * @return $this
     */
    public function setAppliedToTemplateTypes(array $templateTypes);

    /**
     * @param string $templateType
     * @return $this
     */
    public function addAppliedToTemplateType($templateType);

    /**
     * @return array
     */
    public function getAppliedToTemplateTypes();

    /**
     * @param array $templateTypes
     * @return $this
     */
    public function setExcludedFromTemplateTypes(array $templateTypes);

    /**
     * @param string $templateType
     * @return $this
     */
    public function addExcludedFromTemplateType($templateType);

    /**
     * @return array
     */
    public function getExcludedFromTemplateTypes();

    /**
     * @return bool
     */
    public function isAppliedToAllTemplateTypes();

    /**
     * @param bool $appiedToAllTemplateTypes
     * @return $this
     */
    public function setAppliedToAllTemplateTypes($appiedToAllTemplateTypes);
    /**
     * @param array $technologies
     * @return $this
     */
    public function setAppliedToTechnologies(array $technologies);

    /**
     * @param string $technology
     * @return $this
     */
    public function addAppliedToTechnology($technology);

    /**
     * @return array
     */
    public function getAppliedToTechnologies();

    /**
     * @param array $technologies
     * @return $this
     */
    public function setExcludedFromTechnologies(array $technologies);

    /**
     * @param string $technology
     * @return $this
     */
    public function addExcludedFromTechnology($technology);

    /**
     * @return array
     */
    public function getExcludedFromTechnologies();

    /**
     * @return bool
     */
    public function isAppliedToAllTechnologies();

    /**
     * @param bool $appiedToAllTechnologies
     * @return $this
     */
    public function setAppliedToAllTechnologies($appiedToAllTechnologies);
}
