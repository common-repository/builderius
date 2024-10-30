<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Content\Template;

use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\SettingBundle\Factory\SettingValue\BuilderiusSettingValueFactoryInterface;
use Builderius\Bundle\SettingBundle\Generator\FinalSettingValue\FinalSettingValueGeneratorInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValue;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetAwareInterface;

class BuilderiusTemplateModulesWithAssetsContentProvider extends AbstractBuilderiusTemplateContentProvider
{
    const CONTENT_TYPE = 'modulesWithAssets';
    const CONTENT_EXTRA_TYPE = 'modulesWithAssetsContextPart';

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
        return static::CONTENT_TYPE;
    }

    /**
     * @param AssetAwareInterface $module
     * @return bool
     */
    protected function checkAssets(AssetAwareInterface $module)
    {
        return !empty($module->getAssets());
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
        $modulesWithAssets = [];
        if (isset($contentConfig[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY])) {
            foreach ($contentConfig[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY] as $moduleConfig) {
                if (isset($moduleConfig['name'])) {
                    $module = $this->modulesProvider->getModule($moduleConfig['name'], $templateType, $technology);
                    if ($module instanceof AssetAwareInterface && $this->checkAssets($module)) {
                        $data = ['name' => $module->getName()];
                        $moduleAssetConditionContext = [
                            'id' => $moduleConfig['id']
                        ];
                        foreach ($moduleConfig['settings'] as $settingData) {
                            $setting = $module->getSetting($settingData['name']);
                            $setting->resetValues();
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
                                    $data['visibilityCondition'] = $this->finalSettingValueGenerator->generateFinalSettingValue(
                                        $settingValue,
                                        $valueExpression,
                                        $setting->getValueSchema()
                                    );
                                } elseif ($setting->getContentType() === static::CONTENT_EXTRA_TYPE) {
                                    $settingValue = $this->settingValueFactory->create([
                                        BuilderiusSettingCssAwareInterface::CSS_FIELD => false,
                                        BuilderiusSettingValue::VALUE_FIELD => $settingData['value']
                                    ]);
                                    $setting->addValue($settingValue);
                                    $settingValues = $setting->getValues();
                                    $settingValue = reset($settingValues);
                                    $valueExpressions = $setting->getValueExpressions();
                                    foreach ($valueExpressions as $valueExpression) {
                                        $moduleAssetConditionContext[$valueExpression->getName()] =
                                            $this->finalSettingValueGenerator->generateFinalSettingValue(
                                                $settingValue,
                                                $valueExpression,
                                                $setting->getValueSchema()
                                            );
                                    }
                                }
                                $setting->resetValues();
                            }
                        }
                        if (!isset($data['visibilityCondition'])) {
                            $data['visibilityCondition'] = 'true==true';
                        }
                        $data['assetConditionContext'] = $moduleAssetConditionContext;

                        if (!in_array($data, $modulesWithAssets)) {
                            $modulesWithAssets[] = $data;
                        }
                    }
                }
            }
        }

        return $modulesWithAssets;
    }
}