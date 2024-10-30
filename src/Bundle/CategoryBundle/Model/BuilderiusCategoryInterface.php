<?php

namespace Builderius\Bundle\CategoryBundle\Model;

interface BuilderiusCategoryInterface
{
    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

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
     * @param bool $translated
     * @return string
     */
    public function getLabel($translated = true);

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * @return array
     */
    public function getGroups();

    /**
     * @param array $groups
     * @return $this
     */
    public function setGroups(array $groups);

    /**
     * @return bool
     */
    public function isEditable();

    /**
     * @param bool $editable
     * @return $this
     */
    public function setEditable($editable);

    /**
     * @return bool
     */
    public function isDefault();

    /**
     * @param bool $default
     * @return $this
     */
    public function setDefault($default);
}
