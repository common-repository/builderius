<?php

namespace Builderius\Bundle\DeliverableBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

abstract class AbstractBuilderiusDeliverable extends ParameterBag implements BuilderiusDeliverableInterface
{
    const ID_FIELD = 'id';
    const TAG_FIELD = 'tag';
    const DESCRIPTION_FIELD = 'description';
    const SUB_MODULES_FIELD = 'sub_modules';
    const SUB_MODULES_BY_TYPE_FIELD = 'sub_modules_by_type';
    const AUTHOR_FIELD = 'author';
    const CREATED_AT_FIELD = 'created_at';
    const STATUS_FIELD = 'status';

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
    public function getTag()
    {
        return $this->get(self::TAG_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setTag($tag)
    {
        $this->set(self::TAG_FIELD, $tag);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->get(self::DESCRIPTION_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        $this->set(self::DESCRIPTION_FIELD, $description);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSubModules($entityType = null, $technology = null, $type = null)
    {
        $field = $this->get(self::SUB_MODULES_FIELD);

        return $field instanceof \Closure ? $field($entityType, $type, $technology) : $field;
    }

    /**
     * @inheritDoc
     */
    public function setSubModules(array $subModules)
    {
        $modules = [];
        $modulesByType = [];
        foreach ($subModules as $subModule) {
            $modules[$subModule->getTemplateType()][$subModule->getTemplateTechnology()][] = $subModule;
            $modulesByType[$subModule->getTemplateType()][$subModule->getTemplateTechnology()][$subModule->getType()] = $subModule;
        }
        $this->set(self::SUB_MODULES_FIELD, $modules);
        $this->set(self::SUB_MODULES_BY_TYPE_FIELD, $modulesByType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAuthor()
    {
        return $this->get(self::AUTHOR_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setAuthor(\WP_User $author)
    {
        $this->set(self::AUTHOR_FIELD, $author);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->get(self::CREATED_AT_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->set(self::CREATED_AT_FIELD, $createdAt);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->get(self::STATUS_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        $this->set(self::STATUS_FIELD, $status);

        return $this;
    }
}