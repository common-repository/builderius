<?php

namespace Builderius\Bundle\SettingBundle\EventListener;

use Builderius\Bundle\DeliverableBundle\Event\BuilderiusDSMPostCreationEvent;
use Builderius\Bundle\SettingBundle\Model\BuilderiusGlobalSettingsSetInterface;

class BuilderiusGlobalSettingsSetDSMPostCreationEventListener
{
    /**
     * @param BuilderiusDSMPostCreationEvent $event
     */
    public function onDSMPostCreation(BuilderiusDSMPostCreationEvent $event)
    {
        $owner = $event->getVcsOwner();
        if ($owner instanceof BuilderiusGlobalSettingsSetInterface) {
            $event
                ->setType($owner->getType())
                ->setEntityType('global_settings_set')
                ->setTitle(explode('(technology', str_replace('Global settings ', '', $owner->getTitle()))[0]);
            $event->stopPropagation();
        }
    }
}