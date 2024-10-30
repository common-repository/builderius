<?php

namespace Builderius\Bundle\DeliverableBundle\Event;

use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableInterface;
use Builderius\Symfony\Contracts\EventDispatcher\Event;

class BuilderiusDeliverableFromPostCreationEvent extends Event
{
    /**
     * @var \WP_Post
     */
    private $post;

    /**
     * @var BuilderiusDeliverableInterface
     */
    private $deliverable;

    /**
     * @param \WP_Post $post
     */
    public function __construct(\WP_Post $post)
    {
        $this->post = $post;
    }

    /**
     * @return \WP_Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param \WP_Post $post
     * @return $this
     */
    public function setPost(\WP_Post $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return BuilderiusDeliverableInterface
     */
    public function getDeliverable()
    {
        return $this->deliverable;
    }

    /**
     * @param BuilderiusDeliverableInterface $deliverable
     * @return $this
     */
    public function setDeliverable(BuilderiusDeliverableInterface $deliverable)
    {
        $this->deliverable = $deliverable;

        return $this;
    }
}