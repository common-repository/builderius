<?php

namespace Builderius\Bundle\VCSBundle\Event;

use Builderius\Bundle\VCSBundle\Model\BuilderiusVCSOwnerInterface;
use Builderius\Symfony\Contracts\EventDispatcher\Event;

class VCSOwnerTypeResolvingEvent extends Event
{
    /**
     * @var BuilderiusVCSOwnerInterface
     */
    private $inputValue;

    /**
     * @var string
     */
    private $typeName;

    /**
     * @param BuilderiusVCSOwnerInterface $inputValue
     */
    public function __construct(BuilderiusVCSOwnerInterface $inputValue)
    {
        $this->inputValue = $inputValue;
    }

    /**
     * @return BuilderiusVCSOwnerInterface
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
     * @return VCSOwnerTypeResolvingEvent
     */
    public function setTypeName(string $typeName)
    {
        $this->typeName = $typeName;

        return $this;
    }
}