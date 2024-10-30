<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Content\Template;

use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\ResponsiveBundle\Provider\BuilderiusResponsiveStrategiesProviderInterface;
use Builderius\Bundle\ResponsiveBundle\Strategy\BuilderiusResponsiveStrategyInterface;
use Builderius\Bundle\ResponsiveBundle\Strategy\DesktopFirstBuilderiusResponsiveStrategy;
use Builderius\Bundle\SettingBundle\Event\SettingContainingEvent;
use Builderius\Bundle\SettingBundle\Factory\SettingValue\BuilderiusSettingValueFactoryInterface;
use Builderius\Bundle\SettingBundle\Generator\FinalSettingValue\FinalSettingValueGeneratorInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAtRuleInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssValue;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssValueInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValue;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueInterface;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Builderius\Symfony\Component\Templating\EngineInterface;

class BuilderiusTemplateAtRulesContentProvider extends AbstractBuilderiusTemplateContentProvider
{
    const CONTENT_TYPE = 'at_rules';
    const URL = 'url';
    const CONDITION = 'condition';

	/**
	 * @var BuilderiusSettingValueFactoryInterface
	 */
	private $settingValueFactory;

    /**
     * @var BuilderiusSettingsRegistryInterface
     */
    private $settingsRegistry;

	/**
	 * @var BuilderiusModulesProviderInterface
	 */
	private $modulesProvider;

	/**
	 * @var ExpressionLanguage
	 */
	private $expressionLanguage;

    /**
     * @var FinalSettingValueGeneratorInterface
     */
    private $finalSettingValueGenerator;

	/**
	 * @param BuilderiusSettingValueFactoryInterface $settingValueFactory
     * @param BuilderiusSettingsRegistryInterface $settingsRegistry
	 * @param BuilderiusModulesProviderInterface $modulesProvider
	 * @param ExpressionLanguage $expressionLanguage
     * @param FinalSettingValueGeneratorInterface $finalSettingValueGenerator
	 */
	public function __construct(
		BuilderiusSettingValueFactoryInterface $settingValueFactory,
        BuilderiusSettingsRegistryInterface $settingsRegistry,
		BuilderiusModulesProviderInterface $modulesProvider,
		ExpressionLanguage $expressionLanguage,
        FinalSettingValueGeneratorInterface $finalSettingValueGenerator
	) {
		$this->settingValueFactory = $settingValueFactory;
		$this->settingsRegistry = $settingsRegistry;
		$this->modulesProvider = $modulesProvider;
		$this->expressionLanguage = $expressionLanguage;
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
            return null;
        }
	    $templateType = $contentConfig['template']['type'];
	    $hierarchicalConfig =
			ConfigToHierarchicalConfigConverter::convert($contentConfig);
		$cssAtRulesConfig = [];
        if (isset($contentConfig['template']['settings']) && !empty($contentConfig['template']['settings'])) {
            foreach ($contentConfig['template']['settings'] as $templateSettingConfig) {
                $templateSetting = $this->settingsRegistry->getSetting(
                    $templateType,
                    $technology,
                    $templateSettingConfig['name']
                );
                if ($templateSetting) {
                    if ($templateSetting instanceof BuilderiusSettingCssAwareInterface && $templateSetting->hasAtRules()) {
                        foreach ($templateSettingConfig['value'] as $mediaQuery => $pseudoClassData) {
                            foreach ($pseudoClassData as $pseudoClass => $value) {
                                $settingValue = $this->settingValueFactory->create([
                                    BuilderiusSettingCssAwareInterface::CSS_FIELD => true,
                                    BuilderiusSettingValue::VALUE_FIELD => $value,
                                    BuilderiusSettingCssValue::PSEUDO_CLASS_FIELD => $pseudoClass,
                                    BuilderiusSettingCssValue::MEDIA_QUERY_FIELD => $mediaQuery
                                ]);
                                $templateSetting->addValue($settingValue);
                            }
                        }
                        foreach ($templateSetting->getAtRules() as $atRule) {
                            /** @var BuilderiusSettingCssValueInterface $cssValue */
                            foreach ($templateSetting->getValues() as $cssValue) {
                                $atRuleString = $this->formatCssAtRule($cssValue, $atRule);
                                if ($atRuleString !== null) {
                                    $cssAtRulesConfigItem = [
                                        self::URL => $atRuleString,
                                        self::CONDITION => null
                                    ];
                                    if (!in_array($cssAtRulesConfigItem, $cssAtRulesConfig)) {
                                        $cssAtRulesConfig[] = $cssAtRulesConfigItem;
                                    }
                                }
                            }
                        }
                    }
                    $templateSetting->resetValues();
                }
            }
        }
		foreach ($hierarchicalConfig as $moduleConfig) {
			$cssAtRulesConfig = $this->formatModuleCssAtRulesConfig(
			    $templateType,
                $technology,
                $moduleConfig,
                $cssAtRulesConfig
            );
		}

		return $cssAtRulesConfig;
	}

	/**
     * @param string $templateType
     * @param string $templateTechnology
	 * @param array $moduleConfig
	 * @param array $cssAtRulesConfig
     * @throws \Exception
	 * @return array
	 */
	private function formatModuleCssAtRulesConfig(
	    $templateType,
        $templateTechnology,
        array $moduleConfig,
        array $cssAtRulesConfig
    ) {
		$module = $this->modulesProvider->getModule($moduleConfig['name'], $templateType, $templateTechnology);
        if ($module) {
            $vsExpression = null;
            $vcSettingData = array_filter($moduleConfig['settings'], function ($sConf) {
                return $sConf['name'] === 'visibilityCondition';
            });
            if (!empty($vcSettingData)) {
                $vcSettingData = reset($vcSettingData);
                $setting = $module->getSetting($vcSettingData['name']);
                $settingValue = $this->settingValueFactory->create([
                    BuilderiusSettingCssAwareInterface::CSS_FIELD => false,
                    BuilderiusSettingValue::VALUE_FIELD => $vcSettingData['value']
                ]);
                $setting->addValue($settingValue);
                $settingValues = $setting->getValues();
                $settingValue = reset($settingValues);
                $valueExpressions = $setting->getValueExpressions();
                $valueExpression = reset($valueExpressions);
                $vsExpression = $this->finalSettingValueGenerator->generateFinalSettingValue(
                    $settingValue,
                    $valueExpression,
                    $setting->getValueSchema()
                );
                $setting->resetValues();
            }
            foreach ($moduleConfig['settings'] as $settingData) {
                $setting = $module->getSetting($settingData['name']);
                if ($setting) {
                    if ($setting instanceof BuilderiusSettingCssAwareInterface && $setting->hasAtRules()) {
                        foreach ($settingData['value'] as $mediaQuery => $pseudoClassData) {
                            foreach ($pseudoClassData as $pseudoClass => $value) {
                                $settingValue = $this->settingValueFactory->create([
                                    BuilderiusSettingCssAwareInterface::CSS_FIELD => true,
                                    BuilderiusSettingValue::VALUE_FIELD => $value,
                                    BuilderiusSettingCssValue::PSEUDO_CLASS_FIELD => $pseudoClass,
                                    BuilderiusSettingCssValue::MEDIA_QUERY_FIELD => $mediaQuery
                                ]);
                                $setting->addValue($settingValue);
                            }
                        }
                        foreach ($setting->getAtRules() as $atRule) {
                            /** @var BuilderiusSettingCssValueInterface $cssValue */
                            foreach ($setting->getValues() as $cssValue) {
                                $atRuleString = $this->formatCssAtRule($cssValue, $atRule);
                                if ($atRuleString !== null) {
                                    $cssAtRulesConfigItem = [
                                        self::URL => $atRuleString,
                                        self::CONDITION => $vsExpression
                                    ];
                                    if (!in_array($cssAtRulesConfigItem, $cssAtRulesConfig)) {
                                        $cssAtRulesConfig[] = $cssAtRulesConfigItem;
                                    }
                                }
                            }
                        }
                    }
                    $setting->resetValues();
                }
            }
            if (isset($moduleConfig['children'])) {
                foreach ($moduleConfig['children'] as $childrenModuleConfig) {
                    $cssAtRulesConfig = $this->formatModuleCssAtRulesConfig(
                        $templateType,
                        $templateTechnology,
                        $childrenModuleConfig,
                        $cssAtRulesConfig
                    );
                }
            }
        }

		return $cssAtRulesConfig;
	}

	/**
	 * @param BuilderiusSettingCssValueInterface|BuilderiusSettingValueInterface $value
	 * @param BuilderiusSettingCssAtRuleInterface $atRule
	 * @return string|null
	 */
	private function formatCssAtRule(
		BuilderiusSettingCssValueInterface $value,
		BuilderiusSettingCssAtRuleInterface $atRule
	) {
		$context = $value->getValue();
		foreach ($context as $k => $v) {
			if (is_string($context[$k]) && !empty($context[$k])) {
                $context[$k] = str_replace(' ', '+', $v);
            }
		}
		if ($this->expressionLanguage->evaluate($atRule->getConditionExpression(), $context) === true) {
			return sprintf(
				"@%s %s;",
				$atRule->getIdentifier(),
				$this->expressionLanguage->evaluate($atRule->getRuleExpression(), $context)
			);
		}

		return null;
	}
}
