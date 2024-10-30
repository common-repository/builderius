<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Provider\PluginsVersionsProvider;

class BuilderiusBuilderVersionsScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'versions';

    /**
     * @var PluginsVersionsProvider
     */
    private $pluginsVersionsProvider;

    /**
     * @param PluginsVersionsProvider $pluginsVersionsProvider
     */
    public function __construct(PluginsVersionsProvider $pluginsVersionsProvider)
    {
        $this->pluginsVersionsProvider = $pluginsVersionsProvider;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $versions = [];
        foreach ($this->pluginsVersionsProvider->getPluginsVersions() as $plugin => $version) {
            if (strpos($plugin, '.php') === false) {
                $versions[$plugin] = $version;
            }
        }

        return $versions;
    }
}
