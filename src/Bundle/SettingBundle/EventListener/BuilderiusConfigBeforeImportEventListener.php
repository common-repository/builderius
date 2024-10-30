<?php

namespace Builderius\Bundle\SettingBundle\EventListener;

use Builderius\Bundle\ImportExportBundle\Event\ConfigExportEvent;
use Builderius\Bundle\ImportExportBundle\Event\ConfigImportEvent;
use Builderius\Bundle\SettingBundle\Factory\BuilderiusGlobalSettingsSetFromPostFactory;
use Builderius\Bundle\SettingBundle\Model\BuilderiusGlobalSettingsSet;
use Builderius\Bundle\SettingBundle\Registration\BuilderiusGlobalSettingsSetPostType;

class BuilderiusConfigBeforeImportEventListener
{
    /**
     * @var BuilderiusGlobalSettingsSetFromPostFactory
     */
    private $gssFromPostFactory;

    /**
     * @param BuilderiusGlobalSettingsSetFromPostFactory $gssFromPostFactory
     */
    public function __construct(
        BuilderiusGlobalSettingsSetFromPostFactory $gssFromPostFactory
    ) {
        $this->gssFromPostFactory = $gssFromPostFactory;
    }

    /**
     * @param ConfigImportEvent $event
     */
    public function onConfigImport(ConfigImportEvent $event)
    {
        $config = $event->getConfig();
        if (isset($config[ConfigExportEvent::OWNER_TYPE]) && $config[ConfigExportEvent::OWNER_TYPE] === 'global_settings_set') {
            $importEntityPost = $event->getImportEntityPost();
            if ($importEntityPost->post_type !== BuilderiusGlobalSettingsSetPostType::POST_TYPE) {
                throw new \Exception('You are trying to import config for global_settings_set into non-global_settings_set');
            }
            /** @var BuilderiusGlobalSettingsSet $gss */
            $gss = $this->gssFromPostFactory->createGlobalSettingsSet($importEntityPost);
            if ($gss->getTechnology() !== $config['template']['technology']) {
                throw new \Exception('Config technology is not same as technology of import entity');
            }
            if ($gss->getType() !== $config['template']['type']) {
                throw new \Exception('Config type is not same as type of import entity');
            }
            unset($config[ConfigExportEvent::OWNER_TYPE]);
            $event->setConfig($config);
        }
    }
}