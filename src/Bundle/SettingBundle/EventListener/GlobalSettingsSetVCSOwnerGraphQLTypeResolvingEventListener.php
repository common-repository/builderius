<?php

namespace Builderius\Bundle\SettingBundle\EventListener;

use Builderius\Bundle\SettingBundle\Model\BuilderiusGlobalSettingsSetInterface;
use Builderius\Bundle\VCSBundle\Event\VCSOwnerTypeResolvingEvent;

class GlobalSettingsSetVCSOwnerGraphQLTypeResolvingEventListener
{
    public function onTypeResolving(VCSOwnerTypeResolvingEvent $event)
    {
        $value = $event->getInputValue();
        if ($value instanceof BuilderiusGlobalSettingsSetInterface) {
            $event->setTypeName('BuilderiusGlobalSettingsSet');
        }
    }
}