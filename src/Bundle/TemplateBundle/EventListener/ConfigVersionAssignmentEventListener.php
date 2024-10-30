<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Provider\PluginsVersionsProvider;

class ConfigVersionAssignmentEventListener
{
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
     * @param ConfigContainingEvent $event
     */
    public function beforeConfigSave(ConfigContainingEvent $event)
    {
        $config = $event->getConfig();
        $config['version'] = $this->getPluginsVersions();
        $event->setConfig($config);
    }

    /**
     * @return array
     */
    private function getPluginsVersions()
    {
        $versions = [];
        foreach ($this->pluginsVersionsProvider->getPluginsVersions() as $name => $version) {
            if (strpos($name, '.php') === false) {
                $versions[$name] = $version;
            }
        }

        return $versions;
    }
}