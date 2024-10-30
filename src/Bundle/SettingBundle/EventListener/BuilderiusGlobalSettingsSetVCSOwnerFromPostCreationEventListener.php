<?php

namespace Builderius\Bundle\SettingBundle\EventListener;

use Builderius\Bundle\SettingBundle\Factory\BuilderiusGlobalSettingsSetFromPostFactory;
use Builderius\Bundle\SettingBundle\Model\BuilderiusGlobalSettingsSetInterface;
use Builderius\Bundle\SettingBundle\Registration\BuilderiusGlobalSettingsSetPostType;
use Builderius\Bundle\VCSBundle\Event\BuilderiusVCSOwnerFromPostCreationEvent;

class BuilderiusGlobalSettingsSetVCSOwnerFromPostCreationEventListener
{
    /**
     * @var BuilderiusGlobalSettingsSetFromPostFactory
     */
    private $globalSettingsSetFromPostFactory;

    /**
     * @param BuilderiusGlobalSettingsSetFromPostFactory $globalSettingsSetFromPostFactory
     */
    public function __construct(
        BuilderiusGlobalSettingsSetFromPostFactory $globalSettingsSetFromPostFactory
    ) {
        $this->globalSettingsSetFromPostFactory = $globalSettingsSetFromPostFactory;
    }

    public function createOwner(BuilderiusVCSOwnerFromPostCreationEvent $event)
    {
        $post = $event->getPost();
        if ($post instanceof \WP_Post && $post->post_type === BuilderiusGlobalSettingsSetPostType::POST_TYPE) {
            $gSetSet = $this->globalSettingsSetFromPostFactory->createGlobalSettingsSet($post);
            if ($gSetSet instanceof BuilderiusGlobalSettingsSetInterface) {
                $event->setOwner($gSetSet);
            }
        }
    }
}