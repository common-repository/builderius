<?php

namespace Builderius\Bundle\ReleaseBundle\Factory;

use Builderius\Bundle\DeliverableBundle\Factory\BuilderiusDeliverable\Chain\Element\AbstractBuilderiusDeliverableFromPostFactoryChainElement;
use Builderius\Bundle\ReleaseBundle\Registration\BulderiusReleasePostType;

class BuilderiusDeliverableReleaseFromPostFactory extends AbstractBuilderiusDeliverableFromPostFactoryChainElement
{
    /**
     * @var BuilderiusReleaseFromPostFactory
     */
    private $releaseFromPostFactory;

    /**
     * @param BuilderiusReleaseFromPostFactory $releaseFromPostFactory
     */
    public function __construct(BuilderiusReleaseFromPostFactory $releaseFromPostFactory)
    {
        $this->releaseFromPostFactory = $releaseFromPostFactory;
    }

    /**
     * @inheritDoc
     */
    public function isApplicable(\WP_Post $post)
    {
        return $post->post_type === BulderiusReleasePostType::POST_TYPE;
    }

    /**
     * @inheritDoc
     */
    public function create(\WP_Post $post)
    {
        return $this->releaseFromPostFactory->createRelease($post);
    }
}