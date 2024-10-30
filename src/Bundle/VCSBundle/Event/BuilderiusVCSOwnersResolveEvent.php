<?php

namespace Builderius\Bundle\VCSBundle\Event;

use Builderius\Bundle\VCSBundle\Model\BuilderiusVCSOwnerInterface;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Contracts\EventDispatcher\Event;

class BuilderiusVCSOwnersResolveEvent extends Event
{
    /**
     * @var mixed
     */
    private $objectValue;

    /**
     * @var array
     */
    private $args;

    /**
     * @var mixed
     */
    private $context;

    /**
     * @var ResolveInfo
     */
    private $info;

    /**
     * @var BuilderiusVCSOwnerInterface[]
     */
    private $vscOwners = [];

    /**
     * @param $objectValue
     * @param array $args
     * @param $context
     * @param ResolveInfo $info
     */
    public function __construct($objectValue, array $args, $context, ResolveInfo $info) {
        $this->objectValue = $objectValue;
        $this->args = $args;
        $this->context = $context;
        $this->info = $info;
    }

    /**
     * @return mixed
     */
    public function getObjectValue()
    {
        return $this->objectValue;
    }

    /**
     * @param mixed $objectValue
     * @return $this
     */
    public function setObjectValue($objectValue)
    {
        $this->objectValue = $objectValue;

        return $this;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param array $args
     * @return $this
     */
    public function setArgs(array $args)
    {
        $this->args = $args;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param mixed $context
     * @return $this
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return ResolveInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param ResolveInfo $info
     * @return $this
     */
    public function setInfo(ResolveInfo $info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * @return BuilderiusVCSOwnerInterface[]
     */
    public function getVscOwners()
    {
        return $this->vscOwners;
    }

    /**
     * @param BuilderiusVCSOwnerInterface[] $vscOwners
     * @return $this
     */
    public function setVscOwners(array $vscOwners)
    {
        $this->vscOwners = $vscOwners;

        return $this;
    }

}