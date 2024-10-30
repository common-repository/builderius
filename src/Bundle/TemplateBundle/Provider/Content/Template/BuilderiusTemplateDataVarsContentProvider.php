<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Content\Template;

use Builderius\Bundle\SettingBundle\Factory\SettingValue\BuilderiusSettingValueFactoryInterface;
use Builderius\Bundle\SettingBundle\Generator\FinalSettingValue\FinalSettingValueGeneratorInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValue;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;

class BuilderiusTemplateDataVarsContentProvider extends AbstractBuilderiusTemplateContentProvider
{
    const CONTENT_TYPE = 'dataVars';

    /**
     * @var BuilderiusSettingValueFactoryInterface
     */
    private $settingValueFactory;

    /**
     * @var BuilderiusSettingsRegistryInterface
     */
    private $settingsRegistry;

    /**
     * @var FinalSettingValueGeneratorInterface
     */
    private $finalSettingValueGenerator;

    /**
     * @param BuilderiusSettingValueFactoryInterface $settingValueFactory
     * @param BuilderiusSettingsRegistryInterface $settingsRegistry
     * @param FinalSettingValueGeneratorInterface $finalSettingValueGenerator
     */
    public function __construct(
        BuilderiusSettingValueFactoryInterface $settingValueFactory,
        BuilderiusSettingsRegistryInterface $settingsRegistry,
        FinalSettingValueGeneratorInterface $finalSettingValueGenerator
    ) {
        $this->settingValueFactory = $settingValueFactory;
        $this->settingsRegistry = $settingsRegistry;
        $this->finalSettingValueGenerator = $finalSettingValueGenerator;
    }

    /**
     * @inheritDoc
     */
    public function getContentType()
    {
        return self::CONTENT_TYPE;
    }

    /**
     * @inheritDoc
     */
    public function getContent($technology, array $contentConfig)
    {
        if (!in_array($technology, $this->technologies)) {
            return [];
        }
        $templateType = $contentConfig['template']['type'];
        $templateTechnology = $contentConfig['template']['technology'];
        $dataVarsValuesConfig = [];
        if (isset($contentConfig['template']['settings']) && !empty($contentConfig['template']['settings'])) {
            foreach ($contentConfig['template']['settings'] as $templateSettingConfig) {
                $templateSetting = $this->settingsRegistry->getSetting(
                    $templateType,
                    $templateTechnology,
                    $templateSettingConfig['name']
                );
                if ($templateSetting && !$templateSetting instanceof BuilderiusSettingCssAwareInterface &&
                    $templateSetting->getContentType() === self::CONTENT_TYPE) {
                    $settingValue = $this->settingValueFactory->create([
                        BuilderiusSettingCssAwareInterface::CSS_FIELD => false,
                        BuilderiusSettingValue::VALUE_FIELD => $templateSettingConfig['value']
                    ]);
                    $templateSetting->addValue($settingValue);
                    $settingValues = $templateSetting->getValues();
                    $settingValue = reset($settingValues);
                    $valueExpressions = $templateSetting->getValueExpressions();
                    foreach ($valueExpressions as $valueExpression) {
                        $dataVarsValuesConfig = $this->finalSettingValueGenerator->generateFinalSettingValue(
                            $settingValue,
                            $valueExpression,
                            $templateSetting->getValueSchema()
                        );
                    }
                    $templateSetting->resetValues();
                }
            }
        }

        return $dataVarsValuesConfig;
    }
}
