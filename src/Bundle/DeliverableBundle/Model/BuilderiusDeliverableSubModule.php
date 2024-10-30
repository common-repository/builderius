<?php

namespace Builderius\Bundle\DeliverableBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusDeliverableSubModule extends ParameterBag implements BuilderiusDeliverableSubModuleInterface
{
    const ID_FIELD = 'id';
    const NAME_FIELD = 'name';
    const ENTITY_TYPE_FIELD = 'entity_type';
    const TYPE_FIELD = 'type';
    const TECHNOLOGY_FIELD = 'technology';
    const CONTENT_CONFIG_FIELD = 'content_config';
    const CONTENT_FIELD = 'content';
    const ATTRIBUTES_FIELD = 'attributes';
    const OWNER_FIELD = 'owner';

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->get(self::ID_FIELD);
    }
    
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->set(self::NAME_FIELD, $name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEntityType()
    {
        return $this->get(self::ENTITY_TYPE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setEntityType($entityType)
    {
        $this->set(self::ENTITY_TYPE_FIELD, $entityType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->get(self::TYPE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        $this->set(self::TYPE_FIELD, $type);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTechnology()
    {
        return $this->get(self::TECHNOLOGY_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setTechnology($technology)
    {
        $this->set(self::TECHNOLOGY_FIELD, $technology);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContentConfig()
    {
        return $this->get(self::CONTENT_CONFIG_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setContentConfig(array $contentConfig)
    {
        $this->set(self::CONTENT_CONFIG_FIELD, $contentConfig);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContent($contentType = null)
    {
        $content = $this->get(self::CONTENT_FIELD);
        if ($contentType === null) {
            return $content;
        }
        if (is_array($content) && array_key_exists($contentType, $content)) {
            return $content[$contentType];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function setContent(array $content = null)
    {
        $this->set(self::CONTENT_FIELD, $content);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes()
    {
        return $this->get(self::ATTRIBUTES_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($name)
    {
        $attributes = $this->getAttributes();

        return isset($attributes[$name]) ? $attributes[$name] : null;
    }

    /**
     * @inheritDoc
     */
    public function setAttributes(array $attributes)
    {
        $this->set(self::ATTRIBUTES_FIELD, $attributes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOwner()
    {
        return $this->get(self::OWNER_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setOwner(BuilderiusDeliverableInterface $owner)
    {
        $this->set(self::OWNER_FIELD, $owner);

        return $this;
    }
}
