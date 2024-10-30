<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\ImportExportBundle\Event\ConfigExportEvent;
use Builderius\Bundle\ImportExportBundle\Event\ConfigImportEvent;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;

class BuilderiusConfigBeforeImportEventListener
{
    /**
     * @var BuilderiusTemplateFromPostFactory
     */
    private $templateFromPostFactory;

    /**
     * @param BuilderiusTemplateFromPostFactory $templateFromPostFactory
     */
    public function __construct(
        BuilderiusTemplateFromPostFactory $templateFromPostFactory
    ) {
        $this->templateFromPostFactory = $templateFromPostFactory;
    }

    /**
     * @param ConfigImportEvent $event
     */
    public function onConfigImport(ConfigImportEvent $event)
    {
        $config = $event->getConfig();
        if (isset($config[ConfigExportEvent::OWNER_TYPE]) && $config[ConfigExportEvent::OWNER_TYPE] === 'template') {
            $importEntityPost = $event->getImportEntityPost();
            if ($importEntityPost->post_type !== BuilderiusTemplatePostType::POST_TYPE) {
                throw new \Exception('You are trying to import config for template into non-template');
            }
            $template = $this->templateFromPostFactory->createTemplate($importEntityPost);
            if ($template->getTechnology() !== $config['template']['technology']) {
                throw new \Exception('Config technology is not same as technology of import entity');
            }
            if ($template->getType() !== $config['template']['type']) {
                throw new \Exception('Config type is not same as type of import entity');
            }
            unset($config[ConfigExportEvent::OWNER_TYPE]);
            $event->setConfig($config);
        }
    }
}