<?php

namespace Builderius\Bundle\SettingBundle\Registry;

use Builderius\Bundle\SettingBundle\Checker\Setting\BuilderiusSettingCheckerInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingPathInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;

class BuilderiusSettingsRegistry implements BuilderiusSettingsRegistryInterface
{
    /**
     * @var BuilderiusSettingInterface[]
     */
    private $settings = [];

    /**
     * @var BuilderiusSettingCheckerInterface
     */
    private $checker;

    /**
     * @var BuilderiusTemplateTypesProviderInterface
     */
    private $templateTypesProvider;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param BuilderiusSettingCheckerInterface $checker
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        BuilderiusSettingCheckerInterface $checker,
        BuilderiusTemplateTypesProviderInterface $templateTypesProvider,
        BuilderiusRuntimeObjectCache $cache
    ) {
        $this->checker = $checker;
        $this->templateTypesProvider = $templateTypesProvider;
        $this->cache = $cache;
    }

    /**
     * @param BuilderiusSettingInterface $setting
     */
    public function addSetting(BuilderiusSettingInterface $setting)
    {
        if ($this->checker->check($setting)) {
            $templateTypes = [];
            if ($setting->isAppliedToAllTemplateTypes()) {
                if (!empty($setting->getExcludedFromTemplateTypes())) {
                    $templateTypes = [];
                    foreach ($this->templateTypesProvider->getTypes() as $templateType) {
                        foreach ($setting->getExcludedFromTemplateTypes() as $excludedType) {
                            if ($excludedType !== $templateType->getName()) {
                                $templateTypes[] = $templateType;
                            }
                        }
                    }
                } else {
                    $templateTypes = $this->templateTypesProvider->getTypes();
                }
            } elseif (!$setting->isAppliedToAllTemplateTypes() && !empty($setting->getAppliedToTemplateTypes())) {
                $templateTypes = [];
                foreach ($this->templateTypesProvider->getTypes() as $templateType) {
                    foreach ($setting->getAppliedToTemplateTypes() as $appliedType) {
                        if ($appliedType === $templateType->getName()) {
                            $templateTypes[] = $templateType;
                        }
                    }
                }
            }
            foreach ($templateTypes as $templateType) {
                foreach ($templateType->getTechnologies() as $technology) {
                    if ($setting->isAppliedToAllTechnologies()) {
                        if (!in_array($technology->getName(), $setting->getExcludedFromTechnologies())) {
                            $this->settings[$templateType->getName()][$technology->getName()][$setting->getName()] = $setting;
                            if ($setting->isAppliedToAllTemplateTypes() && empty($setting->getExcludedFromTemplateTypes())) {
                                $this->settings['all'][$technology->getName()][$setting->getName()] = $setting;
                            }
                        }
                    } elseif (!$setting->isAppliedToAllTechnologies() && !empty($setting->getAppliedToTechnologies())) {
                        if (in_array($technology->getName(), $setting->getAppliedToTechnologies())) {
                            $this->settings[$templateType->getName()][$technology->getName()][$setting->getName()] = $setting;
                            if ($setting->isAppliedToAllTemplateTypes() && empty($setting->getExcludedFromTemplateTypes())) {
                                $this->settings['all'][$technology->getName()][$setting->getName()] = $setting;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings($templateType, $technology, $sort = true)
    {
        $settings = $this->cache->get(sprintf('builderius_%s_%s_settings_%s', $templateType, $technology, $sort));
        if (false === $settings) {
            if (isset($this->settings[$templateType][$technology])) {
                $settings = $this->settings[$templateType][$technology];
                if ($sort) {
                    $settings = $this->sortSettings($settings);
                }
                $this->cache->set(sprintf('builderius_%s_%s_settings_%s', $templateType, $technology, $sort), $settings);
            } else {
                $settings = [];
            }
        }

        return $settings;
    }

    /**
     * @param BuilderiusSettingInterface[] $settings
     * @return BuilderiusSettingInterface[]
     */
    private function sortSettings(array $settings)
    {
        usort($settings, function (BuilderiusSettingInterface $a, BuilderiusSettingInterface $b) {
            $aPaths = $a->getPaths();
            /** @var BuilderiusSettingPathInterface $aPath */
            $aPath = reset($aPaths);
            $aKey = sprintf('%s/%09d', $aPath->getCategory()->getName(), $a->getSortOrder());
            $bPaths = $b->getPaths();
            /** @var BuilderiusSettingPathInterface $bPath */
            $bPath = reset($bPaths);
            $bKey = sprintf('%s/%09d', $bPath->getCategory()->getName(), $b->getSortOrder());
            if ($aKey < $bKey) {
                return -1;
            } elseif ($aKey > $bKey) {
                return 1;
            } else {
                return 0;
            }
        });

        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function getSetting($templateType, $technology, $name)
    {
        if ($this->hasSetting($templateType, $technology, $name)) {
            return $this->getSettings($templateType, $technology, false)[$name];
        }
        
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasSetting($templateType, $technology, $name)
    {
        return isset($this->getSettings($templateType, $technology, false)[$name]);
    }
}
