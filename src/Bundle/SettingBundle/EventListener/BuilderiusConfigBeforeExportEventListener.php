<?php

namespace Builderius\Bundle\SettingBundle\EventListener;

use Builderius\Bundle\ImportExportBundle\Event\ConfigExportEvent;
use Builderius\Bundle\SettingBundle\Model\BuilderiusGlobalSettingsSetInterface;

class BuilderiusConfigBeforeExportEventListener
{
    /**
     * @param ConfigExportEvent $event
     */
    public function onConfigExport(ConfigExportEvent $event)
    {
        $branch = $event->getBranch();
        $owner = $branch->getOwner();
        if ($owner instanceof BuilderiusGlobalSettingsSetInterface) {
            $config = $event->getConfig();
            $config[ConfigExportEvent::OWNER_TYPE] = 'global_settings_set';
            $event->setConfig($config);
        }
    }
}