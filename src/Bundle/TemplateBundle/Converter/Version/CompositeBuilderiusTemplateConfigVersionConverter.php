<?php

namespace Builderius\Bundle\TemplateBundle\Converter\Version;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Provider\PluginNameForClassProvider;

class CompositeBuilderiusTemplateConfigVersionConverter implements BuilderiusTemplateConfigVersionConverterInterface
{
    /**
     * @var BuilderiusTemplateConfigVersionConverterInterface[]
     */
    private $converters = [];

    /**
     * @var PluginNameForClassProvider
     */
    private $pluginNameForClassProvider;

    public function __construct(PluginNameForClassProvider $pluginNameForClassProvider)
    {
        $this->pluginNameForClassProvider = $pluginNameForClassProvider;
    }

    /**
     * @param BuilderiusTemplateConfigVersionConverterInterface $converter
     * @return $this
     */
    public function addConverter(BuilderiusTemplateConfigVersionConverterInterface $converter)
    {
        $this->converters[] = $converter;

        return $this;
    }

    /**
     * @param string|null $plugin
     * @param string|null $version
     * @return BuilderiusTemplateConfigVersionConverterInterface[]
     */
    public function getConverters($plugin = null, $version = null)
    {
        $filtered = $this->filterConverters();
        if ($plugin && isset($filtered[$plugin])) {
            $filtered = $filtered[$plugin];
        }
        if ($version && isset($filtered[$version])) {
            $filtered = $filtered[$version];
        }

        return $filtered;
    }

    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function convert(array $config)
    {
        $versions = isset($config['version']) ? $config['version'] : [];
        $filteredConverters = $this->filterConverters($versions);
        if (empty($filteredConverters)) {
            return $config;
        }
        $newVersions = [];
        foreach ($filteredConverters as $plugin => $pluginConverters) {
            foreach ($pluginConverters as $version => $versionConverters) {
                /** @var BuilderiusTemplateConfigVersionConverterInterface $versionConverter */
                foreach ($versionConverters as $versionConverter) {
                    $config = $versionConverter->convert($config);
                }
                $newVersions[$plugin] = $version;
            }
        }
        if (isset($config['version'])) {
            foreach ($newVersions as $plugin => $newVersion) {
                $config['version'][$plugin] = $newVersion;
            }
        }
        else {
            $config['version'] = $newVersions;
        }
        return $config;
    }

    /**
     * @param array $versions
     * @return array
     */
    private function filterConverters(array $versions = [])
    {
        $filtered = [];
        foreach ($this->converters as $converter) {
            $converterPlugin = $this->pluginNameForClassProvider->getPluginName(get_class($converter), false);
            if (empty($versions) || (isset($versions[$converterPlugin]) &&
                    version_compare($converter->getVersion(), $versions[$converterPlugin]) === 1))
            {
                $filtered[$converterPlugin][$converter->getVersion()][] = $converter;
            }
        }
        if (!empty($filtered)) {
            foreach ($filtered as &$filteredByPlugin) {
                uksort($filteredByPlugin, function ($a, $b) {
                    $res = version_compare($a, $b);

                    return $res;
                });
                foreach ($filteredByPlugin as &$versionConverters) {
                    if (count($versionConverters) > 1) {
                        usort(
                            $versionConverters,
                            function (
                                BuilderiusTemplateConfigVersionConverterInterface $a,
                                BuilderiusTemplateConfigVersionConverterInterface $b
                            ) {
                                $aOrder = 0;
                                if ($a instanceof BuilderiusTemplateConfigVersionOrderedConverterInterface) {
                                    $aOrder = $a->getOrder();
                                }
                                $bOrder = 0;
                                if ($b instanceof BuilderiusTemplateConfigVersionOrderedConverterInterface) {
                                    $bOrder = $b->getOrder();
                                }
                                if ($aOrder === $bOrder) {
                                    return 0;
                                } elseif ($aOrder < $bOrder) {
                                    return -1;
                                } else {
                                    return 1;
                                }
                            }
                        );
                    }
                }
            }
        }

        return $filtered;
    }
}