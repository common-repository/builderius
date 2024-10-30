<?php

namespace Builderius\Bundle\SettingBundle\Model;

interface BuilderiusSettingFacadeInterface
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
     * @return string
     */
    public function getShowIf();

    /**
     * @param string $showIf
     * @return $this
     */
    public function setShowIf($showIf);

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
     * @return BuilderiusSettingInterface
     */
    public function getSetting();

    /**
     * @param BuilderiusSettingInterface $setting
     * @return $this
     */
    public function setSetting(BuilderiusSettingInterface $setting);

    /**
     * @param string $conditionExpression
     * @return $this
     */
    public function setSettingToFacadeConditionExpression($conditionExpression);

    /**
     * @return string
     */
    public function getSettingToFacadeConditionExpression();

    /**
     * @param string $expression
     * @return $this
     */
    public function setSettingToFacadeExpression($expression);

    /**
     * @return string
     */
    public function getSettingToFacadeExpression();

    /**
     * @param string $conditionExpression
     * @return $this
     */
    public function setFacadeToSettingConditionExpression($conditionExpression);

    /**
     * @return string|null
     */
    public function getFacadeToSettingConditionExpression();

    /**
     * @param string $expression
     * @return $this
     */
    public function setFacadeToSettingExpression($expression);

    /**
     * @return string
     */
    public function getFacadeToSettingExpression();

    /**
     * @param string $expression
     * @return $this
     */
    public function setSettingBasePath($expression);

    /**
     * @return string
     */
    public function getSettingBasePath();
}