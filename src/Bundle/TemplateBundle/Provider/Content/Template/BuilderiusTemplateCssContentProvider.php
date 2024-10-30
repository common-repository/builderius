<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Content\Template;

use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\ResponsiveBundle\Provider\BuilderiusResponsiveStrategiesProviderInterface;
use Builderius\Bundle\ResponsiveBundle\Strategy\BuilderiusResponsiveStrategyInterface;
use Builderius\Bundle\ResponsiveBundle\Strategy\DesktopFirstBuilderiusResponsiveStrategy;
use Builderius\Bundle\SettingBundle\Event\SettingContainingEvent;
use Builderius\Bundle\SettingBundle\Factory\SettingValue\BuilderiusSettingValueFactoryInterface;
use Builderius\Bundle\SettingBundle\Generator\FinalSettingValue\FinalSettingValueGeneratorInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssValue;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssValueInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValue;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Builderius\Symfony\Component\Templating\EngineInterface;

class BuilderiusTemplateCssContentProvider extends AbstractBuilderiusTemplateContentProvider
{
    const CONTENT_TYPE = 'css';
    const MAIN_SELECTOR = '.builderiusContent';

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

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
	 * @var BuilderiusResponsiveStrategiesProviderInterface
	 */
	private $responsiveStrategiesProvider;

	/**
	 * @var EngineInterface
	 */
	private $templatingEngine;

	/**
	 * @var array
	 */
	private $templateMediaQueries = [];

    /**
     * @var FinalSettingValueGeneratorInterface
     */
    private $finalSettingValueGenerator;

    /**
     * @var BuilderiusResponsiveStrategyInterface
     */
    private $responsiveStrategy;

    /**
     * @var array
     */
    private $visibilityConditions = [];

	/**
     * @param EventDispatcherInterface $eventDispatcher
	 * @param BuilderiusSettingValueFactoryInterface $settingValueFactory
     * @param BuilderiusSettingsRegistryInterface $settingsRegistry
	 * @param BuilderiusModulesProviderInterface $modulesProvider
	 * @param BuilderiusResponsiveStrategiesProviderInterface $responsiveStrategiesProvider
	 * @param EngineInterface $templatingEngine
     * @param FinalSettingValueGeneratorInterface $finalSettingValueGenerator
	 */
	public function __construct(
        EventDispatcherInterface $eventDispatcher,
		BuilderiusSettingValueFactoryInterface $settingValueFactory,
        BuilderiusSettingsRegistryInterface $settingsRegistry,
		BuilderiusModulesProviderInterface $modulesProvider,
        BuilderiusResponsiveStrategiesProviderInterface $responsiveStrategiesProvider,
		EngineInterface $templatingEngine,
        FinalSettingValueGeneratorInterface $finalSettingValueGenerator
	) {
	    $this->eventDispatcher = $eventDispatcher;
		$this->settingValueFactory = $settingValueFactory;
		$this->settingsRegistry = $settingsRegistry;
		$this->modulesProvider = $modulesProvider;
		$this->responsiveStrategiesProvider = $responsiveStrategiesProvider;
		$this->templatingEngine = $templatingEngine;
		$this->finalSettingValueGenerator = $finalSettingValueGenerator;
		$this->responsiveStrategy = $this->responsiveStrategiesProvider
            ->getResponsiveStrategy(DesktopFirstBuilderiusResponsiveStrategy::NAME);
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
		$cssPropertiesConfig = [];
		$this->visibilityConditions = [];
        if (isset($contentConfig['template']['settings']) && !empty($contentConfig['template']['settings'])) {
            foreach ($contentConfig['template']['settings'] as $templateSettingConfig) {
                if ($templateSettingConfig['name'] === 'responsiveStrategy' && isset($templateSettingConfig['value']) &&
                    isset($templateSettingConfig['value']['a1']) && $templateSettingConfig['value']['a1'] !== null) {
                    $templateResponsiveStrategy = $this->responsiveStrategiesProvider->getResponsiveStrategy($templateSettingConfig['value']['a1']);
                    if (null !== $templateResponsiveStrategy) {
                        $this->responsiveStrategy = $templateResponsiveStrategy;
                    }
                }
                $templateSetting = $this->settingsRegistry->getSetting(
                    $templateType,
                    $technology,
                    $templateSettingConfig['name']
                );
                if ($templateSetting && $templateSetting instanceof BuilderiusSettingCssAwareInterface &&
                    $templateSetting->getContentType() === self::CONTENT_TYPE) {
                    foreach ($templateSettingConfig['value'] as $mediaQuery => $pseudoClassData) {
                        foreach ($pseudoClassData as $pseudoClass => $value) {
                            if (empty($pseudoClass)) {
                                continue;
                            }
                            $settingValue = $this->settingValueFactory->create([
                                BuilderiusSettingCssAwareInterface::CSS_FIELD => true,
                                BuilderiusSettingValue::VALUE_FIELD => $value,
                                BuilderiusSettingCssValue::PSEUDO_CLASS_FIELD => $pseudoClass,
                                BuilderiusSettingCssValue::MEDIA_QUERY_FIELD => $mediaQuery
                            ]);
                            $templateSetting->addValue($settingValue);
                        }
                    }
                    $event = new SettingContainingEvent($templateSetting);
                    $this->eventDispatcher->dispatch($event, 'builderius_setting_before_generate_final_value');
                    $templateSetting = $event->getSetting();
                    /** @var BuilderiusSettingCssValueInterface $cssValue */
                    foreach ($templateSetting->getValues() as $cssValue) {
                        $validMediaQuery = $cssValue->getMediaQuery();
                        if (!in_array($validMediaQuery, $this->templateMediaQueries)) {
                            $this->templateMediaQueries[] = $validMediaQuery;
                        }
                        $pseudoClass = $cssValue->getPseudoClass();
                        $identifier = self::MAIN_SELECTOR;
                        if ($pseudoClass !== BuilderiusSettingCssValue::DEFAULT_PSEUDO_CLASS) {
                            $identifier = sprintf('%s%s', $identifier, $pseudoClass);
                        }
                        $identifier = str_replace('  ', ' ', $identifier);
                        $valueExpressions = $templateSetting->getValueExpressions();
                        $finalSettingValue = null;
                        foreach ($valueExpressions as $valueExpression) {
                            $finalSettingValue = $this->finalSettingValueGenerator->generateFinalSettingValue(
                                $cssValue,
                                $valueExpression,
                                $templateSetting->getValueSchema()
                            );
                            if ($finalSettingValue !== null) {
                                $name = strtolower(implode('-', preg_split('/(?=[A-Z])/', $valueExpression->getName())));
                                $finalSettingValue = $name === '' ?
                                    sprintf('%s;', $finalSettingValue) :
                                    sprintf('%s: %s;', $name, $finalSettingValue);
                                $finalSettingValue = str_replace(';;', ';', $finalSettingValue);
                                if  ($templateSetting->getName() === 'cssVars') {
                                    if (isset($cssPropertiesConfig[$validMediaQuery][$identifier]) &&
                                        !empty($cssPropertiesConfig[$validMediaQuery][$identifier])) {
                                        array_unshift(
                                            $cssPropertiesConfig[$validMediaQuery][$identifier],
                                            $finalSettingValue
                                        );
                                    } else {
                                        $cssPropertiesConfig[$validMediaQuery][$identifier]
                                        [] = $finalSettingValue;
                                    }
                                } else {
                                    $cssPropertiesConfig[$validMediaQuery][$identifier]
                                    [] = $finalSettingValue;
                                }
                            }
                        }
                    }
                    $templateSetting->resetValues();
                }
            }
        }
		foreach ($hierarchicalConfig as $moduleConfig) {
			$cssPropertiesConfig = $this->formatModuleCssPropertiesConfig(
			    $templateType,
                $technology,
                $moduleConfig,
                $cssPropertiesConfig
            );
		}
		/*foreach ($cssPropertiesConfig as $medQuery => $respConfig) {
			$newRespConfig = [];
            $newRespConfigVc = [];
			foreach ($respConfig as $identifier => $config) {
				$hasVisibilityCondition = false;
				foreach (array_keys($this->visibilityConditions) as $id) {
				    if (strpos($identifier, $id) !== false) {
                        $hasVisibilityCondition = true;
                        break;
                    }
                }
			    if (!isset($newRespConfig[json_encode($config)])) {
					$newRespConfig[json_encode($config)] = $identifier;
				} else {
			        if (!$hasVisibilityCondition) {
                        $newRespConfig[json_encode($config)] = sprintf(
                            '%s, %s',
                            $newRespConfig[json_encode($config)],
                            $identifier
                        );
                    } else {
                        $newRespConfigVc[json_encode($config)] = $identifier;
                    }
				}
			}
			$newRespConfig = array_flip($newRespConfig);
			foreach ($newRespConfig as $identifier => $config) {
				$newRespConfig[$identifier] = json_decode($config, true);
			}
			$cssPropertiesConfig[$medQuery] = $newRespConfig;
            $newRespConfigVc = array_flip($newRespConfigVc);
            foreach ($newRespConfigVc as $identifier => $config) {
                $cssPropertiesConfig[$medQuery][$identifier] = json_decode($config, true);
            }
		}*/
		foreach ($cssPropertiesConfig as &$respCssPropertiesConfig) {
		    uksort($respCssPropertiesConfig, function ($a, $b) {
		        if (strpos($a, $b) !== false) {
		            return 1;
                } elseif (strpos($b, $a) !== false) {
		            return -1;
                }

		        return 0;
            });
        }
        $notSortedMediaQueries = $this->templateMediaQueries;
        if (($key = array_search(BuilderiusSettingCssValue::DEFAULT_MEDIA_QUERY, $notSortedMediaQueries, true)) !== false) {
            unset($notSortedMediaQueries[$key]);
        }
        $sortedMediaQueries = $this->responsiveStrategy->sort($notSortedMediaQueries);
        array_unshift($sortedMediaQueries, 'all');

		$css = $this->templatingEngine->render(
            'BuilderiusTemplateBundle:templateCss.twig',
			[
				'propertiesConfig' => $cssPropertiesConfig,
				'sortedMediaQueries' => $sortedMediaQueries,
                'visibilityConditions' => $this->visibilityConditions
			]
		);
        preg_match_all('/\[\[\[(.*?)\]\]\]/s', $css, $nonEscapedDataVars);
        $nonEscapedDataVarsNames = array_unique($nonEscapedDataVars[1]);

        foreach ($nonEscapedDataVarsNames as $nonEscapedDataVarName) {
            $countOpen = substr_count($nonEscapedDataVarName,'[');
            $countClose = substr_count($nonEscapedDataVarName,']');
            $diff = $countOpen - $countClose;
            if ($diff > 0) {
                $prefix = ']';
                for ($i = 1; $i < $diff; $i++) {
                    $prefix = $prefix . ']';
                }
                $nonEscapedDataVarName = $nonEscapedDataVarName . $prefix;
            }
            $css = str_replace(
                sprintf("[[[%s]]]", $nonEscapedDataVarName),
                sprintf("[^builderius_data_var('%s')|raw^]", trim($nonEscapedDataVarName)),
                $css
            );
        }
        preg_match_all('/\[\[(.*?)\]\]/s', $css, $escapedDataVars);
        $escapedDataVarsNames = array_unique($escapedDataVars[1]);

        foreach ($escapedDataVarsNames as $escapedDataVarName) {
            $countOpen = substr_count($escapedDataVarName,'[');
            $countClose = substr_count($escapedDataVarName,']');
            $diff = $countOpen - $countClose;
            if ($diff > 0) {
                $prefix = ']';
                for ($i = 1; $i < $diff; $i++) {
                    $prefix = $prefix . ']';
                }
                $escapedDataVarName = $escapedDataVarName . $prefix;
            }
            $css = str_replace(
                sprintf("[[%s]]", $escapedDataVarName),
                sprintf("[^builderius_data_var_escaped('%s')|raw^]", trim($escapedDataVarName)),
                $css
            );
        }
        preg_match_all('/--(.*?): \[$builderius_data_var\(\'(.*?)\'\)\|raw^];/s', $css, $dynamicCssVars);
        $dynamicCssVars = array_unique($dynamicCssVars[0]);
        foreach ($dynamicCssVars as $dynamicCssVar) {
            $parts = explode(': ', $dynamicCssVar);
            $name = $parts[0];
            $value = str_replace('[^', '', str_replace('|raw^];', '', $parts[1]));
            $css = str_replace(
                $dynamicCssVar,
                sprintf("[^builderius_dynamic_css_var('%s', %s)|raw^]", trim($name), trim($value)),
                $css
            );
        }
        preg_match_all('/--(.*?): \[$builderius_data_var_escaped\(\'(.*?)\'\)\|raw^];/s', $css, $dynamicCssVars);
        $dynamicCssVars = array_unique($dynamicCssVars[0]);
        foreach ($dynamicCssVars as $dynamicCssVar) {
            $parts = explode(': ', $dynamicCssVar);
            $name = $parts[0];
            $value = str_replace('[^', '', str_replace('|raw^];', '', $parts[1]));
            $css = str_replace(
                $dynamicCssVar,
                sprintf("[^builderius_dynamic_css_var('%s', %s)|raw^]", trim($name), trim($value)),
                $css
            );
        }

        return $css;
	}

	/**
     * @param string $templateType
     * @param string $templateTechnology
	 * @param array $moduleConfig
	 * @param array $cssPropertiesConfig
     * @throws \Exception
	 * @return array
	 */
	private function formatModuleCssPropertiesConfig(
	    $templateType,
        $templateTechnology,
        array $moduleConfig,
        array $cssPropertiesConfig
    ) {
		$moduleIdentificator = sprintf('.uni-node-%s', $moduleConfig['id']);
		$module = $this->modulesProvider->getModule($moduleConfig['name'], $templateType, $templateTechnology);
		if ($module) {
            foreach ($moduleConfig['settings'] as $settingData) {
                $setting = $module->getSetting($settingData['name']);
                if ($setting) {
                    if ($settingData['name'] === 'visibilityCondition') {
                        $settingValue = $this->settingValueFactory->create([
                            BuilderiusSettingCssAwareInterface::CSS_FIELD => false,
                            BuilderiusSettingValue::VALUE_FIELD => $settingData['value']
                        ]);
                        $setting->addValue($settingValue);
                        $settingValues = $setting->getValues();
                        $settingValue = reset($settingValues);
                        $valueExpressions = $setting->getValueExpressions();
                        $valueExpression = reset($valueExpressions);
                        $this->visibilityConditions[$moduleConfig['id']] = $this->finalSettingValueGenerator->generateFinalSettingValue(
                            $settingValue,
                            $valueExpression,
                            $setting->getValueSchema()
                        );
                    } elseif ($setting instanceof BuilderiusSettingCssAwareInterface &&
                        $setting->getContentType() === self::CONTENT_TYPE) {
                        foreach ($settingData['value'] as $mediaQuery => $pseudoClassData) {
                            foreach ($pseudoClassData as $pseudoClass => $value) {
                                if (empty($pseudoClass)) {
                                    continue;
                                }
                                $settingValue = $this->settingValueFactory->create([
                                    BuilderiusSettingCssAwareInterface::CSS_FIELD => true,
                                    BuilderiusSettingValue::VALUE_FIELD => $value,
                                    BuilderiusSettingCssValue::PSEUDO_CLASS_FIELD => $pseudoClass,
                                    BuilderiusSettingCssValue::MEDIA_QUERY_FIELD => $mediaQuery
                                ]);
                                $setting->addValue($settingValue);
                            }
                        }
                        $event = new SettingContainingEvent($setting);
                        $this->eventDispatcher->dispatch($event, 'builderius_setting_before_generate_final_value');
                        $setting = $event->getSetting();
                        /** @var BuilderiusSettingCssValueInterface $cssValue */
                        foreach ($setting->getValues() as $cssValue) {
                            $validMediaQuery = $cssValue->getMediaQuery();
                            if (!in_array($validMediaQuery, $this->templateMediaQueries)) {
                                $this->templateMediaQueries[] = $validMediaQuery;
                            }
                            $pseudoClass = $cssValue->getPseudoClass();
                            $identifier = $moduleIdentificator;
                            if ($pseudoClass !== BuilderiusSettingCssValue::DEFAULT_PSEUDO_CLASS) {
                                $identifier = sprintf('%s%s', $moduleIdentificator, $pseudoClass);
                            }
                            $identifier = str_replace('  ', ' ', sprintf('%s %s', self::MAIN_SELECTOR, $identifier));
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
                                    $finalSettingValue = str_replace(';;', ';', $finalSettingValue);
                                    $cssPropertiesConfig[$validMediaQuery][$identifier]
                                    [] = $finalSettingValue;
                                }
                            }
                        }
                    }
                    $setting->resetValues();
                }
            }
            if (isset($moduleConfig['children'])) {
                foreach ($moduleConfig['children'] as $childrenModuleConfig) {
                    $cssPropertiesConfig =
                        $this->formatModuleCssPropertiesConfig(
                            $templateType,
                            $templateTechnology,
                            $childrenModuleConfig,
                            $cssPropertiesConfig
                        );
                }
            }
        }

		return $cssPropertiesConfig;
	}
}
