<?php

namespace Builderius\Bundle\SettingBundle\Generator\GlobalSetting;

use Builderius\Bundle\SettingBundle\Factory\SettingValue\BuilderiusSettingValueFactoryInterface;
use Builderius\Bundle\SettingBundle\Generator\FinalSettingValue\FinalSettingValueGeneratorInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssValue;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssValueInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValue;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;
use Builderius\Bundle\SettingBundle\Validation\Rule\MediaQuery;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;

class CssVarsBuilderiusGlobalSettingValueGenerator implements BuilderiusGlobalSettingValueGeneratorInterface
{
    /**
     * @var BuilderiusTemplateTypesProviderInterface
     */
    private $templateTypesProvider;

    /**
     * @var BuilderiusSettingsRegistryInterface
     */
    private $settingsRegistry;

    /**
     * @var BuilderiusSettingValueFactoryInterface
     */
    private $settingValueFactory;

    /**
     * @var FinalSettingValueGeneratorInterface
     */
    private $finalSettingValueGenerator;

    /**
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     * @param BuilderiusSettingsRegistryInterface $settingsRegistry
     * @param BuilderiusSettingValueFactoryInterface $settingValueFactory
     * @param FinalSettingValueGeneratorInterface $finalSettingValueGenerator
     */
    public function __construct(
        BuilderiusTemplateTypesProviderInterface $templateTypesProvider,
        BuilderiusSettingsRegistryInterface $settingsRegistry,
        BuilderiusSettingValueFactoryInterface $settingValueFactory,
        FinalSettingValueGeneratorInterface $finalSettingValueGenerator
    ) {
        $this->templateTypesProvider = $templateTypesProvider;
        $this->settingsRegistry = $settingsRegistry;
        $this->settingValueFactory = $settingValueFactory;
        $this->finalSettingValueGenerator = $finalSettingValueGenerator;
    }


    /**
     * @inheritDoc
     */
    public function getSettingName()
    {
        return 'cssVars';
    }

    /**
     * @inheritDoc
     */
    public function getSettingGroup()
    {
        return 'css';
    }

    /**
     * @inheritDoc
     */
    public function generateSettingValue($technology, array $valueConfig)
    {
        if (isset($valueConfig['all'])) {
            $finalConfig = [];
            $configForAll = $valueConfig['all'];
            foreach ($this->templateTypesProvider->getTypesWithTechnology($technology) as $templateType) {
                $templateTypeName = $templateType->getName();
                if (!isset($valueConfig[$templateTypeName])) {
                    $finalConfig[$templateTypeName] = $configForAll;
                } else {
                    $finalConfig[$templateTypeName] = $valueConfig[$templateTypeName];
                    foreach ($configForAll as $respMode => $respModeConfig) {
                        if (!isset($finalConfig[$templateTypeName][$respMode])) {
                            $finalConfig[$templateTypeName][$respMode] = $configForAll[$respMode];
                        } else {
                            if (isset($finalConfig[$templateTypeName][$respMode]['i1']) && isset($configForAll[$respMode]['i1'])) {
                                $varNames = array_map(
                                    function($varItem){
                                        return $varItem['a2'];
                                    },
                                    $finalConfig[$templateTypeName][$respMode]['i1']
                                );
                                foreach ($configForAll[$respMode]['i1'] as $allVarItem) {
                                    if (!in_array($allVarItem['a2'], $varNames)) {
                                        $finalConfig[$templateTypeName][$respMode]['i1'][] = $allVarItem;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $finalConfig = $valueConfig;
        }
        $cssPropertiesConfig = [];
        foreach ($finalConfig as $type => $finalTypeConfig) {
            $setting = $this->settingsRegistry->getSetting($type, $technology, $this->getSettingName());
            if ($setting) {
                if ($setting instanceof BuilderiusSettingCssAwareInterface) {
                    foreach ($finalTypeConfig as $responsiveModeName => $value) {
                        $settingValue = $this->settingValueFactory->create([
                            BuilderiusSettingCssAwareInterface::CSS_FIELD => true,
                            BuilderiusSettingValue::VALUE_FIELD => $value,
                            BuilderiusSettingCssValue::PSEUDO_CLASS_FIELD => BuilderiusSettingCssValue::DEFAULT_PSEUDO_CLASS,
                            BuilderiusSettingCssValue::MEDIA_QUERY_FIELD => $responsiveModeName
                        ]);
                        $setting->addValue($settingValue);
                    }
                    /** @var BuilderiusSettingCssValueInterface $cssValue */
                    foreach ($setting->getValues() as $cssValue) {
                        $mediaQueryValidator = new MediaQuery();
                        $mediaQuery = $cssValue->getMediaQuery();
                        if ($mediaQuery !== BuilderiusSettingCssValue::DEFAULT_MEDIA_QUERY) {
                            $isMediaQueryValid = $mediaQueryValidator->validate(sprintf('@media %s', $mediaQuery));
                            if ($isMediaQueryValid === false) {
                                throw new \Exception(
                                    sprintf(
                                        'Not valid media query "%s"',
                                        $mediaQuery
                                    )
                                );
                            }
                        }
                        $pseudoClass = $cssValue->getPseudoClass();
                        if ($pseudoClass !== BuilderiusSettingCssValue::DEFAULT_PSEUDO_CLASS) {
                            if (trim($pseudoClass) === '') {
                                throw new \Exception(
                                    'Pseudo Selector can\'t be empty'
                                );
                            }
                        }
                        $valueExpressions = $setting->getValueExpressions();
                        $finalSettingValue = null;
                        foreach ($valueExpressions as $valueExpression) {
                            $finalSettingValue = $this->finalSettingValueGenerator->generateFinalSettingValue(
                                $cssValue,
                                $valueExpression,
                                $setting->getValueSchema()
                            );
                            if ($finalSettingValue !== null) {
                                $name = strtolower(implode('-', preg_split('/(?=[A-Z])/', $valueExpression->getName())));
                                $finalSettingValue = $name === '' ?
                                    sprintf('%s;', $finalSettingValue) :
                                    sprintf('%s: %s;', $name, $finalSettingValue);
                                $cssPropertiesConfig[$type][$this->getSettingGroup()][$mediaQuery] =
                                    sprintf(':root {%s}', $finalSettingValue);
                            }
                        }
                    }
                }
                $setting->resetValues();
            }
        }

        return $cssPropertiesConfig;
    }
}