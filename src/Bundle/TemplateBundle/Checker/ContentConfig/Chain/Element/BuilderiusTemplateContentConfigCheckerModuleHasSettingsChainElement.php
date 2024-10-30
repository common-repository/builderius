<?php

namespace Builderius\Bundle\TemplateBundle\Checker\ContentConfig\Chain\Element;

use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\SettingBundle\Checker\SettingValue\BuilderiusSettingValueCheckerInterface;
use Builderius\Bundle\SettingBundle\Factory\SettingValue\BuilderiusSettingValueFactoryInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssValue;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValue;
use Builderius\Bundle\SettingBundle\Validation\Rule\MediaQuery;

class BuilderiusTemplateContentConfigCheckerModuleHasSettingsChainElement extends
AbstractBuilderiusTemplateContentConfigCheckerChainElement
{
    /**
     * @var BuilderiusModulesProviderInterface
     */
    private $modulesProvider;

    /**
     * @var BuilderiusSettingValueCheckerInterface
     */
    private $settingValueChecker;

    /**
     * @var BuilderiusSettingValueFactoryInterface
     */
    private $settingValueFactory;

    /**
     * @param BuilderiusModulesProviderInterface $modulesProvider
     * @param BuilderiusSettingValueCheckerInterface $settingValueChecker
     * @param BuilderiusSettingValueFactoryInterface $settingValueFactory
     */
    public function __construct(
        BuilderiusModulesProviderInterface $modulesProvider,
        BuilderiusSettingValueCheckerInterface $settingValueChecker,
        BuilderiusSettingValueFactoryInterface $settingValueFactory
    ) {
        $this->modulesProvider = $modulesProvider;
        $this->settingValueChecker = $settingValueChecker;
        $this->settingValueFactory = $settingValueFactory;
    }
    
    /**
     * @inheritDoc
     */
    protected function evaluate(array $configItem, $templateType, $templateTechnology)
    {
        if (!isset($configItem['settings'])) {
            return true;
        }
        $module = $this->modulesProvider->getModule($configItem['name'], $templateType, $templateTechnology);
        $settings = $configItem['settings'];
        $settingsNames = [];
        foreach ($settings as $setting) {
            if (!in_array($setting['name'], $settingsNames, true)) {
                $settingsNames[] = $setting['name'];
            } else {
                throw new \Exception(
                    sprintf(
                        'There are multiple settings with name "%s" added for module "%s"',
                        $setting['name'],
                        $configItem['name']
                    )
                );
            }
            if (!$module->hasSetting($setting['name'])) {
                throw new \Exception(
                    sprintf(
                        'There is no registered setting with name "%s" for module "%s"',
                        $setting['name'],
                        $configItem['name']
                    )
                );
            }
            $moduleSetting = $module->getSetting($setting['name']);
            if ($moduleSetting instanceof BuilderiusSettingCssAwareInterface) {
                $mediaQueryValidator = new MediaQuery();
                foreach ($setting['value'] as $mediaQuery => $pseudoClassData) {
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
                    foreach ($pseudoClassData as $pseudoClass => $value) {
                        /*if ($pseudoClass !== BuilderiusSettingCssValue::DEFAULT_PSEUDO_CLASS) {
                                if (trim($pseudoClass) === '') {
                                    throw new \Exception(
                                        'Pseudo Selector can\'t be empty'
                                    );
                                }
                        }*/
                        $this->settingValueChecker->check(
                            $this->settingValueFactory->create([
                                BuilderiusSettingCssAwareInterface::CSS_FIELD => true,
                                BuilderiusSettingCssValue::MEDIA_QUERY_FIELD => $mediaQuery,
                                BuilderiusSettingCssValue::PSEUDO_CLASS_FIELD => $pseudoClass,
                                BuilderiusSettingValue::VALUE_FIELD => $value
                            ]),
                            $moduleSetting
                        );
                    }
                }
            } else {
                $this->settingValueChecker->check(
                    $this->settingValueFactory->create([
                        BuilderiusSettingCssAwareInterface::CSS_FIELD => false,
                        BuilderiusSettingValue::VALUE_FIELD => $setting['value']
                    ]),
                    $moduleSetting
                );
            }
        }

        return true;
    }
}
