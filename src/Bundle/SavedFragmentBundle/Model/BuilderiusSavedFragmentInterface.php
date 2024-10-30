<?php

namespace Builderius\Bundle\SavedFragmentBundle\Model;

interface BuilderiusSavedFragmentInterface
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
    public function getTitle();

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

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
     * @return string
     */
    public function getCategory();

    /**
     * @param string $category
     * @return $this
     */
    public function setCategory($category);

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
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type);

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

    /**
     * @return array
     */
    public function getDynamicContentConfig();

    /**
     * @param array $contentConfig
     * @return $this
     */
    public function setDynamicContentConfig(array $contentConfig);

    /**
     * @return array
     */
    public function getStaticContentConfig();

    /**
     * @param array $contentConfig
     * @return $this
     */
    public function setStaticContentConfig(array $contentConfig);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * @return \WP_User
     */
    public function getAuthor();

    /**
     * @param \WP_User $author
     * @return $this
     */
    public function setAuthor(\WP_User $author);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $image
     * @return $this
     */
    public function setImage($image);
}