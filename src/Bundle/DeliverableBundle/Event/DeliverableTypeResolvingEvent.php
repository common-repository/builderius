<?php

namespace Builderius\Bundle\DeliverableBundle\Event;

use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableInterface;
use Builderius\Symfony\Contracts\EventDispatcher\Event;

class DeliverableTypeResolvingEvent extends Event
{
    /**
     * @var BuilderiusDeliverableInterface
     */
    private $inputValue;

    /**
     * @var string
     */
    private $typeName;

    /**
     * @param BuilderiusDeliverableInterface $inputValue
     */
    public function __construct(BuilderiusDeliverableInterface $inputValue)
    {
        $this->inputValue = $inputValue;
    }

    /**
     * @return BuilderiusDeliverableInterface
     */
    public function getInputValue()
    {
        return $this->inputValue;
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * @param string $typeName
     * @return $this
     */
    public function setTypeName(string $typeName)
    {
        $this->typeName = $typeName;

        return $this;
    }
}