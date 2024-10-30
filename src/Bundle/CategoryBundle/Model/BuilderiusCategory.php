<?php

namespace Builderius\Bundle\CategoryBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusCategory extends ParameterBag implements BuilderiusCategoryInterface
{
    const ID_FIELD = 'id';
    const NAME_FIELD = 'name';
    const LABEL_FIELD = 'label';
    const SORT_ORDER_FIELD = 'sortOrder';
    const GROUPS_FIELD = 'groups';
    const EDITABLE_FIELD = 'editable';
    const DEFAULT_FIELD = 'default';

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->get(self::ID_FIELD);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->set(self::ID_FIELD, $id);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->set(self::NAME_FIELD, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel($translated = true)
    {
        return $translated === true ? __($this->get(self::LABEL_FIELD), 'builderius') : $this->get(self::LABEL_FIELD);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        $this->set(self::LABEL_FIELD, $label);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->get(self::SORT_ORDER_FIELD, 10);
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($sortOrder)
    {
        $this->set(self::SORT_ORDER_FIELD, $sortOrder);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getGroups()
    {
        return $this->get(self::GROUPS_FIELD);
    }

    /**
     * {@inheritdoc}
     */
    public function setGroups(array $groups)
    {
        $this->set(self::GROUPS_FIELD, $groups);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isEditable()
    {
        return $this->get(self::EDITABLE_FIELD, false);
    }

    /**
     * {@inheritdoc}
     */
    public function setEditable($editable)
    {
        $this->set(self::EDITABLE_FIELD, $editable);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isDefault()
    {
        return $this->get(self::DEFAULT_FIELD, false);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefault($default)
    {
        $this->set(self::DEFAULT_FIELD, $default);

        return $this;
    }
}
