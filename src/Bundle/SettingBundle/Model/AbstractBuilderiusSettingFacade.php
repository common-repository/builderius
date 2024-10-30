<?php

namespace Builderius\Bundle\SettingBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

abstract class AbstractBuilderiusSettingFacade extends ParameterBag implements BuilderiusSettingFacadeInterface
{
    const TYPE = null;

    const NAME_FIELD = 'name';
    const LABEL_FIELD = 'label';
    const PATHS_FIELD = 'paths';
    const SORT_ORDER_FIELD = 'sortOrder';
    const DYNAMIC_DATA_FIELD = 'dynamicData';
    const DYNAMIC_DATA_TYPES_FIELD = 'dynamicDataTypes';
    const SHOW_IF_FIELD = 'showIf';
    const SETTING_COMPONENT_FIELD = 'component';
    const OPTIONS_FIELD = 'options';
    const SETTING_FIELD = 'setting';
    const SETTING_TO_FACADE_CONDITION_EXPRESSION_FIELD = 'settingToFacadeConditionExpression';
    const SETTING_TO_FACADE_EXPRESSION_FIELD = 'settingToFacadeExpression';
    const FACADE_TO_SETTING_CONDITION_EXPRESSION_FIELD = 'facadeToSettingConditionExpression';
    const FACADE_TO_SETTING_EXPRESSION_FIELD = 'facadeToSettingExpression';
    const SETTING_BASE_PATH_FIELD = 'settingBasePath';

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
    public function getSetting()
    {
        return $this->get(self::SETTING_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setSetting(BuilderiusSettingInterface $setting)
    {
        $this->set(self::SETTING_FIELD, $setting);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSettingToFacadeConditionExpression($conditionExpression)
    {
        $this->set(self::SETTING_TO_FACADE_CONDITION_EXPRESSION_FIELD, $conditionExpression);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSettingToFacadeConditionExpression()
    {
        return $this->get(self::SETTING_TO_FACADE_CONDITION_EXPRESSION_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setSettingToFacadeExpression($expression)
    {
        $this->set(self::SETTING_TO_FACADE_EXPRESSION_FIELD, $expression);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSettingToFacadeExpression()
    {
        return $this->get(self::SETTING_TO_FACADE_EXPRESSION_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setFacadeToSettingConditionExpression($conditionExpression)
    {
        $this->set(self::FACADE_TO_SETTING_CONDITION_EXPRESSION_FIELD, $conditionExpression);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFacadeToSettingConditionExpression()
    {
        return $this->get(self::FACADE_TO_SETTING_CONDITION_EXPRESSION_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setFacadeToSettingExpression($expression)
    {
        $this->set(self::FACADE_TO_SETTING_EXPRESSION_FIELD, $expression);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFacadeToSettingExpression()
    {
        return $this->get(self::FACADE_TO_SETTING_EXPRESSION_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setSettingBasePath($expression)
    {
        $this->set(self::SETTING_BASE_PATH_FIELD, $expression);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSettingBasePath()
    {
        return $this->get(self::SETTING_BASE_PATH_FIELD);
    }
}
