<?php

namespace Builderius\Bundle\DeliverableBundle\Event;

use Builderius\Bundle\VCSBundle\Model\BuilderiusVCSOwnerInterface;
use Builderius\Symfony\Contracts\EventDispatcher\Event;

class BuilderiusDSMPostCreationEvent extends Event
{
    /**
     * @var BuilderiusVCSOwnerInterface
     */
    private $vcsOwner;

    /**
     * @var string
     */
    private $tag;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $entityType;

    /**
     * @var string
     */
    private $title;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @param BuilderiusVCSOwnerInterface $vcsOwner
     * @param string $tag
     */
    public function __construct(BuilderiusVCSOwnerInterface $vcsOwner, $tag)
    {
        $this->vcsOwner = $vcsOwner;
        $this->tag = $tag;
    }

    /**
     * @return BuilderiusVCSOwnerInterface
     */
    public function getVcsOwner()
    {
        return $this->vcsOwner;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * @param string $entityType
     * @return $this
     */
    public function setEntityType(string $entityType)
    {
        $this->entityType = $entityType;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }
}