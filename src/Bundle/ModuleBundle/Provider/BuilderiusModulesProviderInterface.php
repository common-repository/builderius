<?php

namespace Builderius\Bundle\ModuleBundle\Provider;

use Builderius\Bundle\ModuleBundle\Model\BuilderiusModuleInterface;

interface BuilderiusModulesProviderInterface
{
    /**
     * @param string $templateType
     * @param string $technology
     * @param bool $withSettings
     * @param array $configVersions
     * @return BuilderiusModuleInterface[]
     */
    public function getModules($templateType, $technology, $withSettings = true, $configVersions = []);

    /**
     * @param string $name
     * @param string $templateType
     * @param string $technology
     * @param bool $withSettings
     * @param array $configVersions
     * @return BuilderiusModuleInterface|null
     */
    public function getModule($name, $templateType, $technology, $withSettings = true, $configVersions = []);
    
    /**
     * @param string $name
     * @param string $templateType
     * @param string $technology
     * @param bool $withSettings
     * @param array $configVersions
     * @return bool
     */
    public function hasModule($name, $templateType, $technology, $withSettings = true, $configVersions = []);
}
