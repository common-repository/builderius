<?php

namespace Builderius\Bundle\SettingBundle\Factory;

use Builderius\Bundle\SettingBundle\Registration\BuilderiusGlobalSettingsSetPostType;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusVCSOwner\Chain\Element\AbstractBuilderiusVCSOwnerFromPostFactoryChainElement;

class BuilderiusVCSOwnerGlobalSettingsSetFromPostFactory extends AbstractBuilderiusVCSOwnerFromPostFactoryChainElement
{
    /**
     * @var BuilderiusGlobalSettingsSetFromPostFactory
     */
    private $globalSettingsSetFromPostFactory;

    /**
     * @param BuilderiusGlobalSettingsSetFromPostFactory $globalSettingsSetFromPostFactory
     */
    public function __construct(BuilderiusGlobalSettingsSetFromPostFactory $globalSettingsSetFromPostFactory)
    {
        $this->globalSettingsSetFromPostFactory = $globalSettingsSetFromPostFactory;
    }

    /**
     * @inheritDoc
     */
    public function isApplicable(\WP_Post $post)
    {
        return $post->post_type === BuilderiusGlobalSettingsSetPostType::POST_TYPE;
    }

    /**
     * @inheritDoc
     */
    public function create(\WP_Post $post)
    {
        return $this->globalSettingsSetFromPostFactory->createGlobalSettingsSet($post);
    }
}