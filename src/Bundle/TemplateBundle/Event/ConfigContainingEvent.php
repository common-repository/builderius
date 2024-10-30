<?php

namespace Builderius\Bundle\TemplateBundle\Event;

use Builderius\Symfony\Contracts\EventDispatcher\Event;

class ConfigContainingEvent extends Event
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var \WP_Error
     */
    private $error;

    /**
     * @param array $config
     */
    public function __construct(array $config = null)
    {
        $this->config = $config;
    }

    /**
     * @return array|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

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