<?php

namespace Builderius\Bundle\TemplateBundle\Model;

interface BuilderiusTemplateSubTypeInterface
{
    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getLabel();

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
     * @return bool
     */
    public function isThemeDisabled();

    /**
     * @param bool $themeDisabled
     * @return $this
     */
    public function setThemeDisabled($themeDisabled);
}