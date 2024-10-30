<?php

namespace Builderius\Bundle\SettingBundle\Model;

abstract class AbstractBuilderiusSettingDecorator implements BuilderiusSettingInterface
{
    /**
     * @var BuilderiusSettingInterface
     */
    protected $setting;

    /**
     * @param BuilderiusSettingInterface $setting
     * @throws \Exception
     */
    public function __construct(BuilderiusSettingInterface $setting)
    {
        $this->setting = $setting;
    }

    public function __call($name, $arguments)
    {
        if ($arguments) {
            return call_user_func_array([$this->setting, $name], $arguments);
        } else {
            return call_user_func([$this->setting, $name]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->setting->getName();
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->setting->setName($name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->setting->getLabel();
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        $this->setting->setLabel($label);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContentType()
    {
        return $this->setting->getContentType();
    }

    /**
     * @inheritDoc
     */
    public function setContentType($contentType)
    {
        $this->setting->setContentType($contentType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isDynamicDataAllowed()
    {
        return $this->setting->isDynamicDataAllowed();
    }

    /**
     * @inheritDoc
     */
    public function setDynamicDataAllowed($dynamicData)
    {
        $this->setting->setDynamicDataAllowed($dynamicData);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDynamicDataTypes()
    {
        return $this->setting->getDynamicDataTypes();
    }

    /**
     * @inheritDoc
     */
    public function setDynamicDataTypes(array $dynamicDataTypes)
    {
        $this->setting->setDynamicDataTypes($dynamicDataTypes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isDisplayLabel()
    {
        return $this->setting->isDisplayLabel();
    }

    /**
     * @inheritDoc
     */
    public function setDisplayLabel($displayLabel)
    {
        $this->setting->setDisplayLabel($displayLabel);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPaths()
    {
        return $this->setting->getPaths();
    }

    /**
     * @inheritDoc
     */
    public function setPaths(array $paths)
    {
        $this->setting->setPaths($paths);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addPath(BuilderiusSettingPathInterface $path)
    {
        $this->setting->addPath($path);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->setting->getSortOrder();
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder($sortOrder)
    {
        $this->setting->setSortOrder($sortOrder);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSettingComponent()
    {
        return $this->setting->getSettingComponent();
    }

    /**
     * @inheritDoc
     */
    public function setSettingComponent($settingComponent)
    {
        $this->setting->setSettingComponent($settingComponent);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOptions()
    {
        return $this->setting->getOptions();
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options)
    {
        $this->setting->setOptions($options);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValues(BuilderiusSettingValuesCollectionInterface $values)
    {
        $this->setting->setValues($values);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addValues(BuilderiusSettingValuesCollectionInterface $values)
    {
        $this->setting->addValues($values);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addValue(BuilderiusSettingValueInterface $value)
    {
        $this->setting->addValue($value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addDefaultValues(BuilderiusSettingValuesCollectionInterface $values, $for = [])
    {
        $this->setting->addDefaultValues($values, $for);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addDefaultValue(BuilderiusSettingValueInterface $value, $for = [])
    {
        $this->setting->addDefaultValue($value, $for);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValues()
    {
        return $this->setting->getValues();
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValues($for = null)
    {
        return $this->setting->getDefaultValues($for);
    }

    /**
     * @inheritDoc
     */
    public function resetValues()
    {
        $this->setting->resetValues();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValueSchema()
    {
        return $this->setting->getValueSchema();
    }

    /**
     * @inheritDoc
     */
    public function setValueSchema($valueSchema)
    {
        $this->setting->setValueSchema($valueSchema);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValueExpressions()
    {
        return $this->setting->getValueExpressions();
    }

    /**
     * @inheritDoc
     */
    public function setValueExpressions(array $valueExpressions)
    {
        $this->setting->setValueExpressions($valueExpressions);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addValueExpression(BuilderiusSettingValueExpressionInterface $valueExpression)
    {
        $this->setting->addValueExpression($valueExpression);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShowIf()
    {
        return $this->setting->getShowIf();
    }

    /**
     * @inheritDoc
     */
    public function setShowIf($showIf)
    {
        $this->setting->setShowIf($showIf);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFacades()
    {
        return $this->setting->getFacades();
    }

    /**
     * @inheritDoc
     */
    public function addFacade(BuilderiusSettingFacadeInterface $facade)
    {
        $this->setting->addFacade($facade);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAppliedToTemplateTypes(array $templateTypes)
    {
        $this->setting->setAppliedToTemplateTypes($templateTypes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addAppliedToTemplateType($templateType)
    {
        $this->setting->addAppliedToTemplateType($templateType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAppliedToTemplateTypes()
    {
        return $this->setting->getAppliedToTemplateTypes();
    }

    /**
     * @inheritDoc
     */
    public function setExcludedFromTemplateTypes(array $templateTypes)
    {
        $this->setting->setExcludedFromTemplateTypes($templateTypes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addExcludedFromTemplateType($templateType)
    {
        $this->setting->addExcludedFromTemplateType($templateType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExcludedFromTemplateTypes()
    {
        return $this->setting->getExcludedFromTemplateTypes();
    }

    /**
     * @inheritDoc
     */
    public function isAppliedToAllTemplateTypes()
    {
        return $this->setting->isAppliedToAllTemplateTypes();
    }

    /**
     * @inheritDoc
     */
    public function setAppliedToAllTemplateTypes($appiedToAllTemplateTypes)
    {
        $this->setting->setAppliedToAllTemplateTypes($appiedToAllTemplateTypes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAppliedToTechnologies(array $technologies)
    {
        $this->setting->setAppliedToTechnologies($technologies);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addAppliedToTechnology($technology)
    {
        $this->setting->addAppliedToTechnology($technology);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAppliedToTechnologies()
    {
        return $this->setting->getAppliedToTechnologies();
    }

    /**
     * @inheritDoc
     */
    public function setExcludedFromTechnologies(array $technologies)
    {
        $this->setting->setExcludedFromTechnologies($technologies);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addExcludedFromTechnology($technology)
    {
        $this->setting->addExcludedFromTechnology($technology);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExcludedFromTechnologies()
    {
        return $this->setting->getExcludedFromTechnologies();
    }

    /**
     * @inheritDoc
     */
    public function isAppliedToAllTechnologies()
    {
        return $this->setting->isAppliedToAllTechnologies();
    }

    /**
     * @inheritDoc
     */
    public function setAppliedToAllTechnologies($appiedToAllTechnologies)
    {
        $this->setting->setAppliedToAllTechnologies($appiedToAllTechnologies);

        return $this;
    }
}