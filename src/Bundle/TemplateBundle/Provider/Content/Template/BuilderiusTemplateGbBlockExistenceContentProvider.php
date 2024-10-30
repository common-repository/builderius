<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Content\Template;

use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\SettingBundle\Factory\SettingValue\BuilderiusSettingValueFactoryInterface;
use Builderius\Bundle\SettingBundle\Generator\FinalSettingValue\FinalSettingValueGeneratorInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValue;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;

class BuilderiusTemplateGbBlockExistenceContentProvider extends AbstractBuilderiusTemplateContentProvider
{
    const CONTENT_TYPE = 'gbBlockExistence';

    /**
     * @var BuilderiusModulesProviderInterface
     */
    private $modulesProvider;

    /**
     * @var BuilderiusSettingValueFactoryInterface
     */
    private $settingValueFactory;

    /**
     * @var FinalSettingValueGeneratorInterface
     */
    private $finalSettingValueGenerator;

    /**
     * @param BuilderiusModulesProviderInterface $modulesProvider
     * @param BuilderiusSettingValueFactoryInterface $settingValueFactory
     * @param FinalSettingValueGeneratorInterface $finalSettingValueGenerator
     */
    public function __construct(
        BuilderiusModulesProviderInterface $modulesProvider,
        BuilderiusSettingValueFactoryInterface $settingValueFactory,
        FinalSettingValueGeneratorInterface $finalSettingValueGenerator
    ) {
        $this->modulesProvider = $modulesProvider;
        $this->settingValueFactory = $settingValueFactory;
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
        $gbBlocks = [];
        if (isset($contentConfig[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY])) {
            foreach ($contentConfig[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY] as $moduleConfig) {
                if (isset($moduleConfig['name']) && $moduleConfig['name'] === 'GutenbergBlock') {
                    $module = $this->modulesProvider->getModule($moduleConfig['name'], $templateType, $technology);
                    $foundVisibilityCondition = false;
                    foreach ($moduleConfig['settings'] as $settingData) {
                        $setting = $module->getSetting($settingData['name']);
                        $setting->resetValues();
                        if ($setting) {
                            if ($settingData['name'] === 'visibilityCondition') {
                                $foundVisibilityCondition = true;
                                $settingValue = $this->settingValueFactory->create([
                                    BuilderiusSettingCssAwareInterface::CSS_FIELD => false,
                                    BuilderiusSettingValue::VALUE_FIELD => $settingData['value']
                                ]);
                                $setting->addValue($settingValue);
                                $settingValues = $setting->getValues();
                                $settingValue = reset($settingValues);
                                $valueExpressions = $setting->getValueExpressions();
                                $valueExpression = reset($valueExpressions);
                                $visibilityCondition = $this->finalSettingValueGenerator->generateFinalSettingValue(
                                    $settingValue,
                                    $valueExpression,
                                    $setting->getValueSchema()
                                );
                                if (!in_array($visibilityCondition, $gbBlocks)) {
                                    $gbBlocks[] = $visibilityCondition;
                                }
                                $setting->resetValues();
                                break;
                            }
                            $setting->resetValues();
                        }
                    }
                    if ($foundVisibilityCondition === false) {
                        if (!in_array('true==true', $gbBlocks)) {
                            $gbBlocks[] = 'true==true';
                        }
                    }
                }
            }
        }

        return $gbBlocks;
    }
}