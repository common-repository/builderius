<?php

namespace Builderius\Bundle\DeliverableBundle\Model;

interface BuilderiusDeliverableSubModuleInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getEntityType();

    /**
     * @param string $entityType
     * @return $this
     */
    public function setEntityType($entityType);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getTechnology();

    /**
     * @param string $technology
     * @return $this
     */
    public function setTechnology($technology);

    /**
     * @return array
     */
    public function getContentConfig();

    /**
     * @param array $contentConfig
     * @return $this
     */
    public function setContentConfig(array $contentConfig);

    /**
     * @return mixed
     */
    public function getContent($contentType = null);

    /**
     * @param array|null $content
     * @return string
     */
    public function setContent(array $content = null);

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @param string $name
     * @return mixed
     */
    public function getAttribute($name);

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes);

    /**
     * @return BuilderiusDeliverableInterface
     */
    public function getOwner();

    /**
     * @param BuilderiusDeliverableInterface $owner
     * @return $this
     */
    public function setOwner(BuilderiusDeliverableInterface $owner);
}