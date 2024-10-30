<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\ImportExportBundle\Event\ConfigExportEvent;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateInterface;

class BuilderiusConfigBeforeExportEventListener
{
    /**
     * @param ConfigExportEvent $event
     */
    public function onConfigExport(ConfigExportEvent $event)
    {
        $branch = $event->getBranch();
        $owner = $branch->getOwner();
        if ($owner instanceof BuilderiusTemplateInterface) {
            $config = $event->getConfig();
            $config[ConfigExportEvent::OWNER_TYPE] = 'template';
            $event->setConfig($config);
        }
    }
}