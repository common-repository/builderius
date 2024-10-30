<?php

namespace Builderius\Bundle\TemplateBundle\Model;

interface BuilderiusTemplateTypeInterface
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
     * @return BuilderiusTemplateTechnologyInterface[]
     */
    public function getTechnologies();

    /**
     * @param string $name
     * @return BuilderiusTemplateTechnologyInterface|null
     */
    public function getTechnology($name);

    /**
     * @param string $name
     * @return boolean
     */
    public function hasTechnology($name);

    /**
     * @param BuilderiusTemplateTechnologyInterface[] $technologies
     * @return $this
     */
    public function setTechnologies(array $technologies);

    /**
     * @param BuilderiusTemplateTechnologyInterface $technology
     * @return $this
     */
    public function addTechnology(BuilderiusTemplateTechnologyInterface $technology);

    /**
     * @return bool
     */
    public function isStandalone();

    /**
     * @param bool $standalone
     * @return $this
     */
    public function setStandalone($standalone);
}