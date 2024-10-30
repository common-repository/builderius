<?php

namespace Builderius\MooMoo\Platform\Bundle\MigrationBundle\Condition;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\AbstractCondition;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Provider\PluginsVersionsProvider;
class LatestMigrationsAreLoadedCondition extends \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\AbstractCondition
{
    /**
     * @var array
     */
    private $pluginsVersions = [];
    /**
     * @param PluginsVersionsProvider $provider
     */
    public function setPluginsVersionsProvider(\Builderius\MooMoo\Platform\Bundle\KernelBundle\Provider\PluginsVersionsProvider $provider)
    {
        $this->pluginsVersions = $provider->getPluginsVersions();
    }
    /**
     * @inheritDoc
     */
    protected function getResult()
    {
        foreach ($this->pluginsVersions as $plugin => $version) {
            $pluginLoadedMigrationsVersion = get_option(\sprintf('%s_loaded_migrations_version', \explode('/', $plugin)[0]), '0');
            if (\version_compare($version, $pluginLoadedMigrationsVersion) === 1) {
                return \false;
            }
        }
        return \true;
    }
}
