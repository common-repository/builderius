<?php

namespace Builderius\Bundle\VCSBundle\Event;

use Builderius\Bundle\VCSBundle\Model\BuilderiusVCSOwnerInterface;
use Builderius\Symfony\Contracts\EventDispatcher\Event;

class BuilderiusVCSOwnerFromPostCreationEvent extends Event
{
    /**
     * @var \WP_Post
     */
    private $post;

    /**
     * @var BuilderiusVCSOwnerInterface
     */
    private $owner;

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
     * @return BuilderiusVCSOwnerInterface
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param BuilderiusVCSOwnerInterface $owner
     * @return $this
     */
    public function setOwner(BuilderiusVCSOwnerInterface $owner)
    {
        $this->owner = $owner;

        return $this;
    }
}