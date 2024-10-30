<?php

namespace Builderius\Bundle\VCSBundle\Event;

use Builderius\Bundle\VCSBundle\Model\BuilderiusBranchInterface;
use Builderius\Symfony\Contracts\EventDispatcher\Event;

class BuilderiusBranchFromPostCreationEvent extends Event
{
    /**
     * @var \WP_Post
     */
    private $post;

    /**
     * @var BuilderiusBranchInterface
     */
    private $branch;

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
     * @return BuilderiusBranchInterface
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param BuilderiusBranchInterface $branch
     * @return $this
     */
    public function setBranch(BuilderiusBranchInterface $branch)
    {
        $this->branch = $branch;

        return $this;
    }
}