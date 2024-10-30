<?php

namespace Builderius\Bundle\LayoutBundle\Model;

interface BuilderiusLayoutInterface
{
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
    public function getLabel();

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * @return array
     */
    public function getTags();

    /**
     * @param array $tags
     * @return $this
     */
    public function setTags(array $tags);

    /**
     * @param string $tag
     * @return $this
     */
    public function addTag($tag);

    /**
     * @return array
     */
    public function getConfig();

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config);

    /**
     * @return string
     */
    public function getCategory();

    /**
     * @param string $category
     * @return $this
     */
    public function setCategory($category);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $image
     * @return $this
     */
    public function setImage($image);

    /**
     * @return array
     */
    public function getTechnologies();

    /**
     * @param array $technologies
     * @return $this
     */
    public function setTechnologies(array $technologies);

    /**
     * @param string $technology
     * @return $this
     */
    public function addTechnology($technology);
}
