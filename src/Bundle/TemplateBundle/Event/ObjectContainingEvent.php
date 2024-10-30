<?php

namespace Builderius\Bundle\TemplateBundle\Event;

use Builderius\Symfony\Contracts\EventDispatcher\Event;

class ObjectContainingEvent extends Event
{
    /**
     * @var object
     */
    private $object;

    /**
     * @var \WP_Error
     */
    private $error;

    /**
     * @param object $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param object $object
     * @return $this
     */
    public function setObject($object)
    {
        $this->object = $object;

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