<?php

namespace Builderius\Bundle\TemplateBundle\Event;

use Builderius\Symfony\Contracts\EventDispatcher\Event;

class PostContainingEvent extends Event
{
    /**
     * @var \WP_Post
     */
    private $post;

    /**
     * @var \WP_Error
     */
    private $error;

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
     * @return \WP_Error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param \WP_Error $error
     * @return $this
     */
    public function setError(\WP_Error $error)
    {
        $this->error = $error;

        return $this;
    }
}