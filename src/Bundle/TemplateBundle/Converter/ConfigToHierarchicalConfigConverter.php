<?php

namespace Builderius\Bundle\TemplateBundle\Converter;

class ConfigToHierarchicalConfigConverter
{
    const CONFIG_MODULES_KEY = 'modules';
    const CONFIG_INDEXES_KEY = 'indexes';

    /**
     * @param array $config
     * @return array
     */
    public static function convert(array $config)
    {
        $modules = isset($config[self::CONFIG_MODULES_KEY]) ? (array)$config[self::CONFIG_MODULES_KEY] : [];
        $indexes = isset($config[self::CONFIG_INDEXES_KEY]) ? (array)$config[self::CONFIG_INDEXES_KEY] : [];
        $indexesOfLevel = isset($indexes['root']) ? (array)$indexes['root'] : [];
        
        return self::getHierarchicalConfig(
            $modules,
            $indexes,
            $indexesOfLevel
        );
    }

    /**
     * @param array $modules
     * @param array $indexes
     * @param array $indexesOfLevel
     * @return array
     */
    private static function getHierarchicalConfig(
        array $modules,
        array $indexes,
        array $indexesOfLevel
    ) {
        $hierarchicalConfig = [];
        foreach ($indexesOfLevel as $levelElement) {
            if (isset($modules[$levelElement])) {
                $hierarchicalConfig[$levelElement] = $modules[$levelElement];
                if (isset($indexes[$levelElement])) {
                    foreach ($indexes[$levelElement] as $childElement) {
                        $hierarchicalConfig[$levelElement]['children'][$childElement] = $modules[$childElement];
                        if (isset($indexes[$childElement])) {
                            $hierarchicalConfig[$levelElement]['children'][$childElement]['children'] =
                                self::getHierarchicalConfig($modules, $indexes, $indexes[$childElement]);
                        }
                    }
                }
            }
        }

        return $hierarchicalConfig;
    }
}
