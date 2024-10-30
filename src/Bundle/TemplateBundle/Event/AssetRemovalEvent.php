<?php

namespace Builderius\Bundle\TemplateBundle\Event;

use Builderius\Symfony\Contracts\EventDispatcher\Event;

class AssetRemovalEvent extends Event
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $result;

    /**
     * @param string $type
     * @param string $name
     * @param bool $result
     */
    public function __construct($type, $name, $result)
    {
        $this->type = $type;
        $this->name = $name;
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param bool $result
     * @return AssetRemovalEvent
     */
    public function setResult(bool $result)
    {
        $this->result = $result;

        return $this;
    }
}