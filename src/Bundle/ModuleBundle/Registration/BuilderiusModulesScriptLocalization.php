<?php

namespace Builderius\Bundle\ModuleBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\CategoryBundle\Provider\BuilderiusCategoriesProviderInterface;
use Builderius\Bundle\ModuleBundle\Model\BuilderiusCompositeModule;
use Builderius\Bundle\ModuleBundle\Model\BuilderiusCompositeModuleInterface;
use Builderius\Bundle\ModuleBundle\Model\BuilderiusContainerModuleInterface;
use Builderius\Bundle\ModuleBundle\Model\BuilderiusModule;
use Builderius\Bundle\ModuleBundle\Model\BuilderiusModuleInterface;
use Builderius\Bundle\ModuleBundle\Model\BuilderiusSavedCompositeModule;
use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssValueInterface;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;

class BuilderiusModulesScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'modules';
    const REGULAR = 'regular';
    const COMPOSITE = 'composite';

    /**
     * @var BuilderiusModulesProviderInterface
     */
    private $modulesProvider;

    /**
     * @var BuilderiusModulesProviderInterface
     */
    private $compositeModulesProvider;

    /**
     * @var BuilderiusCategoriesProviderInterface
     */
    private $categoriesProvider;

    /**
     * @var BuilderiusTemplateProviderInterface
     */
    private $templatesProvider;

    /**
     * @var  BuilderiusTemplateConfigVersionConverterInterface
     */
    private $configVersionConverter;

    /**
     * @var array
     */
    private $allModulesNames = [];

    /**
     * @param BuilderiusModulesProviderInterface $modulesProvider
     * @param BuilderiusModulesProviderInterface $compositeModulesProvider
     * @param BuilderiusCategoriesProviderInterface $categoriesProvider
     * @param BuilderiusTemplateProviderInterface $templatesProvider
     * @param BuilderiusTemplateConfigVersionConverterInterface $configVersionConverter
     */
    public function __construct(
        BuilderiusModulesProviderInterface $modulesProvider,
        BuilderiusModulesProviderInterface $compositeModulesProvider,
        BuilderiusCategoriesProviderInterface $categoriesProvider,
        BuilderiusTemplateProviderInterface $templatesProvider,
        BuilderiusTemplateConfigVersionConverterInterface $configVersionConverter
    ) {
        $this->modulesProvider = $modulesProvider;
        $this->compositeModulesProvider = $compositeModulesProvider;
        $this->categoriesProvider = $categoriesProvider;
        $this->templatesProvider = $templatesProvider;
        $this->configVersionConverter = $configVersionConverter;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $data = [];
        $data[self::REGULAR] = [];
        $data[self::COMPOSITE] = [];
        $template = $this->templatesProvider->getTemplate();
        if ($template) {
            foreach ($this->modulesProvider->getModules($template->getType(), $template->getTechnology()) as $module) {
                $data[self::REGULAR][$module->getName()] = $this->getModuleConfig($module);
            }
            foreach ($this->compositeModulesProvider->getModules($template->getType(), $template->getTechnology()) as $module) {
                $data[self::COMPOSITE][$module->getName()] = $this->getCompositeModuleConfig($module);
            }
        }
        
        return $data;
    }

    /**
     * @param BuilderiusModuleInterface $module
     * @return array
     * @throws \Exception
     */
    private function getModuleConfig(BuilderiusModuleInterface $module)
    {
        $categoryName = $module->getCategory();
        if (!$this->categoriesProvider->hasCategory('module', $categoryName)) {
            throw new \Exception(sprintf('There is no category with name %s in group "module"', $categoryName));
        }

        $config =  [
            BuilderiusModule::NAME_FIELD => $module->getName(),
            BuilderiusModule::ICON_FIELD => $module->getIcon(),
            BuilderiusModule::PUBLIC_FIELD => $module->isPublic(),
            BuilderiusModule::LABEL_FIELD => $module->getLabel(),
            BuilderiusModule::CATEGORY_FIELD => $categoryName,
            BuilderiusModule::SORT_ORDER_FIELD => $module->getSortOrder(),
            BuilderiusModule::TAGS_FIELD => $module->getTags(),
            BuilderiusModule::SETTINGS_FIELD => $this->getSettings($module)
        ];
        if ($module instanceof BuilderiusContainerModuleInterface) {
            if (empty($module->getNotContainerFor())) {
                $containerForModules = $module->getContainerFor();
            } else {
                $containerForModules = array_diff($this->getAllModulesNames(), $module->getNotContainerFor());
                sort($containerForModules);
            }
            $config[BuilderiusContainerModuleInterface::CONTAINER_FIELD] = true;
            $config[BuilderiusContainerModuleInterface::CONTAINER_FOR_FIELD] = $containerForModules;
        } else {
            $config[BuilderiusContainerModuleInterface::CONTAINER_FIELD] = false;
        }

        return $config;
    }

    /**
     * @param BuilderiusModuleInterface $module
     * @return array
     * @throws \Exception
     */
    private function getCompositeModuleConfig(BuilderiusCompositeModuleInterface $module)
    {
        $categoryName = $module->getCategory();
        if (!$this->categoriesProvider->hasCategory('module', $categoryName)) {
            throw new \Exception(sprintf('There is no category with name %s in group "module"', $categoryName));
        }

        $config = [
            BuilderiusModule::NAME_FIELD => $module->getName(),
            BuilderiusModule::ICON_FIELD => $module->getIcon(),
            BuilderiusModule::PUBLIC_FIELD => $module->isPublic(),
            BuilderiusModule::LABEL_FIELD => $module->getLabel(),
            BuilderiusModule::CATEGORY_FIELD => $categoryName,
            BuilderiusModule::SORT_ORDER_FIELD => $module->getSortOrder(),
            BuilderiusModule::TAGS_FIELD => $module->getTags(),
            BuilderiusModule::SETTINGS_FIELD => [],
            BuilderiusContainerModuleInterface::CONTAINER_FIELD => false,
            BuilderiusCompositeModule::CONFIG_FIELD => $this->configVersionConverter->convert($module->getConfig())
        ];
        if ($module instanceof BuilderiusSavedCompositeModule) {
            $config[BuilderiusSavedCompositeModule::ID_FIELD] = $module->getId();
        }

        return $config;
    }

    /**
     * @param BuilderiusModuleInterface $module
     * @return array
     */
    private function getSettings(BuilderiusModuleInterface $module)
    {
        $settings = [];
        foreach ($module->getSettings() as $setting) {
            $defaultValues = $setting->getDefaultValues($module->getName());
            if (!empty($defaultValues)) {
                $settingArray = ['name' => $setting->getName()];
                foreach ($defaultValues as $defaultValue) {
                    if ($defaultValue instanceof BuilderiusSettingCssValueInterface) {
                        $settingArray['value'][$defaultValue->getMediaQuery()][$defaultValue->getPseudoClass()] =
                            $defaultValue->getValue();
                    } else {
                        $settingArray['value'] = $defaultValue->getValue();
                    }
                }
                $settings[] = $settingArray;
            }
        }

        return $settings;
    }

    /**
     * @return array
     */
    private function getAllModulesNames()
    {
        if (empty($this->allModulesNames)) {
            $template = $this->templatesProvider->getTemplate();
            $this->allModulesNames =
                array_keys($this->modulesProvider->getModules($template->getType(), $template->getTechnology()));
        }

        return $this->allModulesNames;
    }
}
