<?php

namespace Builderius\Bundle\SettingBundle\Converter\Facade\ToArray;

use Builderius\Bundle\SettingBundle\Model\AbstractBuilderiusSetting;
use Builderius\Bundle\SettingBundle\Model\AbstractBuilderiusSettingFacade;
use Builderius\Bundle\SettingBundle\Model\BuilderiusModuleSettingInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingFacadeInterface;

class BuilderiusSettingFacadeToArrayConverter implements BuilderiusSettingFacadeToArrayConverterInterface
{
    /**
     * @inheritDoc
     */
    public static function convert(BuilderiusSettingFacadeInterface $facade)
    {
        $paths = [];
        foreach ($facade->getPaths() as $path) {
            $paths[] = sprintf(
                '%s.%s.%s',
                $path->getForm()->getName(),
                $path->getTab()->getName(),
                $path->getCategory()->getName()
            );
        }
        $setting = $facade->getSetting();

        $data = [
            'facade' => true,
            AbstractBuilderiusSettingFacade::NAME_FIELD => $facade->getName(),
            AbstractBuilderiusSettingFacade::LABEL_FIELD => __($facade->getLabel()),
            AbstractBuilderiusSettingFacade::SETTING_FIELD => $setting->getName(),
            AbstractBuilderiusSettingFacade::SETTING_COMPONENT_FIELD => $facade->getSettingComponent(),
            AbstractBuilderiusSettingFacade::SORT_ORDER_FIELD => $facade->getSortOrder(),
            AbstractBuilderiusSettingFacade::DYNAMIC_DATA_FIELD => $facade->isDynamicDataAllowed(),
            AbstractBuilderiusSettingFacade::SHOW_IF_FIELD => $facade->getShowIf(),
            AbstractBuilderiusSettingFacade::OPTIONS_FIELD => self::convertOptions($facade->getOptions()),
            AbstractBuilderiusSettingFacade::PATHS_FIELD => $paths,
            AbstractBuilderiusSettingFacade::SETTING_TO_FACADE_CONDITION_EXPRESSION_FIELD => $facade->getSettingToFacadeConditionExpression(),
            AbstractBuilderiusSettingFacade::SETTING_TO_FACADE_EXPRESSION_FIELD => $facade->getSettingToFacadeExpression(),
            AbstractBuilderiusSettingFacade::FACADE_TO_SETTING_CONDITION_EXPRESSION_FIELD => $facade->getFacadeToSettingConditionExpression(),
            AbstractBuilderiusSettingFacade::FACADE_TO_SETTING_EXPRESSION_FIELD => $facade->getFacadeToSettingExpression(),
            AbstractBuilderiusSettingFacade::SETTING_BASE_PATH_FIELD => $facade->getSettingBasePath()
        ];
        if ($facade->isDynamicDataAllowed()) {
            $data[AbstractBuilderiusSettingFacade::DYNAMIC_DATA_TYPES_FIELD] = $facade->getDynamicDataTypes();
        }
        $applyTo = null;
        if ($setting instanceof BuilderiusModuleSettingInterface) {
            $applyTo = $setting->isAppliedToAllModules() ? [] : $setting->getAppliedToModules();
        }
        if ($facade instanceof BuilderiusModuleSettingInterface) {
            $applyTo = (empty($facade->getAppliedToModules()) || $facade->isAppliedToAllModules()) ? $applyTo : $facade->getAppliedToModules();
        }
        if ($applyTo !== null) {
            $data['applyTo'] = $applyTo;
        }

        return $data;
    }

    /**
     * @param array $options
     * @return array
     */
    private static function convertOptions(array $options)
    {
        if (isset($options['allow_empty'])) {
            if (isset($options['values']) && $options['allow_empty'] === true) {
                array_unshift($options['values'], '');
            }
            unset($options['allow_empty']);
        }

        return $options;
    }
}