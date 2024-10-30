<?php

namespace Builderius\Bundle\SettingBundle\Event;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingInterface;
use Builderius\Symfony\Contracts\EventDispatcher\Event;

class SettingContainingEvent extends Event
{
    /**
     * @var BuilderiusSettingInterface
     */
    private $setting;

    /**
     * @var \WP_Error
     */
    private $error;

    /**
     * @param BuilderiusSettingInterface $setting
     */
    public function __construct(BuilderiusSettingInterface $setting)
    {
        $this->setting = $setting;
    }

    /**
     * @return BuilderiusSettingInterface
     */
    public function getSetting()
    {
        return $this->setting;
    }

    /**
     * @param BuilderiusSettingInterface $setting
     * @return $this
     */
    public function setSetting(BuilderiusSettingInterface $setting)
    {
        $this->setting = $setting;

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