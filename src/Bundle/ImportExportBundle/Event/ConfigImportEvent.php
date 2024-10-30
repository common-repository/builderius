<?php

namespace Builderius\Bundle\ImportExportBundle\Event;

use Builderius\Symfony\Contracts\EventDispatcher\Event;

class ConfigImportEvent extends Event
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var \WP_Post
     */
    private $importEntityPost;

    /**
     * @param array $config
     * @param \WP_Post $importEntityPost
     */
    public function __construct(
        array $config,
        \WP_Post $importEntityPost
    ) {
        $this->config = $config;
        $this->importEntityPost = $importEntityPost;
    }

    /**
     * @return \WP_Post
     */
    public function getImportEntityPost()
    {
        return $this->importEntityPost;
    }

    /**
     * @return array
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
}