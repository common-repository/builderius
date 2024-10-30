<?php

namespace Builderius\Bundle\TemplateBundle\Checker\ContentConfig\Chain\Element;

use Builderius\Bundle\SettingBundle\Checker\SettingValue\BuilderiusSettingValueCheckerInterface;
use Builderius\Bundle\SettingBundle\Factory\SettingValue\BuilderiusSettingValueFactoryInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssValue;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValue;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;
use Builderius\Bundle\SettingBundle\Validation\Rule\MediaQuery;
use Builderius\Bundle\TemplateBundle\Checker\ContentConfig\BuilderiusTemplateContentConfigCheckerInterface;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;

class BuilderiusTemplateContentConfigCheckerTemplateSettingsChainElement implements BuilderiusTemplateContentConfigCheckerInterface
{
    /**
     * @var BuilderiusTemplateContentConfigCheckerInterface|null
     */
    private $successor;

    /**
     * @var BuilderiusTemplateTypesProviderInterface
     */
    private $templateTypesProvider;

    /**
     * @var BuilderiusSettingsRegistryInterface
     */
    private $settingsRegistry;

    /**
     * @var BuilderiusSettingValueCheckerInterface
     */
    private $settingValueChecker;

    /**
     * @var BuilderiusSettingValueFactoryInterface
     */
    private $settingValueFactory;

    /**
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     * @param BuilderiusSettingsRegistryInterface $settingsRegistry
     * @param BuilderiusSettingValueCheckerInterface $settingValueChecker
     * @param BuilderiusSettingValueFactoryInterface $settingValueFactory
     */
    public function __construct(
        BuilderiusTemplateTypesProviderInterface $templateTypesProvider,
        BuilderiusSettingsRegistryInterface $settingsRegistry,
        BuilderiusSettingValueCheckerInterface $settingValueChecker,
        BuilderiusSettingValueFactoryInterface $settingValueFactory
    ) {
        $this->templateTypesProvider = $templateTypesProvider;
        $this->settingsRegistry = $settingsRegistry;
        $this->settingValueChecker = $settingValueChecker;
        $this->settingValueFactory = $settingValueFactory;
    }
    /**
     * @param BuilderiusTemplateContentConfigCheckerInterface $checker
     */
    public function setSuccessor(BuilderiusTemplateContentConfigCheckerInterface $checker)
    {
        $this->successor = $checker;
    }

    /**
     * @return BuilderiusTemplateContentConfigCheckerInterface|null
     */
    protected function getSuccessor()
    {
        return $this->successor;
    }

    /**
     * @inheritDoc
     */
    public function check(array $contentConfig)
    {
        $templateConfig = $contentConfig['template'];
        $type = $templateConfig['type'];
        if ($type !== 'all' && !$this->templateTypesProvider->hasType($type)) {
            throw new \Exception(
                sprintf(
                    'Not registered "%s" templateType',
                    $type
                )
            );
        }
        $technology = $templateConfig['technology'];
        if (isset($templateConfig['settings']) && is_array($templateConfig['settings'])) {
            foreach ($templateConfig['settings'] as $settingConfig) {
                $settingName = $settingConfig['name'];
                $settingValue = $settingConfig['value'];
                $setting = $this->settingsRegistry->getSetting($type, $technology, $settingName);
                if (!$setting) {
                    throw new \Exception(
                        sprintf(
                            'Not registered "%s" setting for "%s" templateType and "%s" technology',
                            $settingName,
                            $type,
                            $technology
                        )
                    );
                }
                if ($setting instanceof BuilderiusSettingCssAwareInterface) {
                    $mediaQueryValidator = new MediaQuery();
                    foreach ($settingValue as $mediaQuery => $pseudoClassData) {
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
                            if ($pseudoClass !== BuilderiusSettingCssValue::DEFAULT_PSEUDO_CLASS) {
                                if (trim($pseudoClass) === '') {
                                    throw new \Exception(
                                        'Pseudo Selector can\'t be empty'
                                    );
                                }
                            }
                            $this->settingValueChecker->check(
                                $this->settingValueFactory->create([
                                    BuilderiusSettingCssAwareInterface::CSS_FIELD => true,
                                    BuilderiusSettingCssValue::MEDIA_QUERY_FIELD => $mediaQuery,
                                    BuilderiusSettingCssValue::PSEUDO_CLASS_FIELD => $pseudoClass,
                                    BuilderiusSettingValue::VALUE_FIELD => $value
                                ]),
                                $setting
                            );
                        }
                    }
                } else {
                    $this->settingValueChecker->check(
                        $this->settingValueFactory->create([
                            BuilderiusSettingCssAwareInterface::CSS_FIELD => false,
                            BuilderiusSettingValue::VALUE_FIELD => $settingValue
                        ]),
                        $setting
                    );
                }
            }
        }

        if ($this->getSuccessor()) {
            return $this->getSuccessor()->check($contentConfig);
        } else {
            return true;
        }
    }
}