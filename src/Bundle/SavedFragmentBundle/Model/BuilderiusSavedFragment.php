<?php

namespace Builderius\Bundle\SavedFragmentBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusSavedFragment extends ParameterBag implements BuilderiusSavedFragmentInterface
{
    const ID_FIELD = 'id';
    const NAME_FIELD = 'name';
    const TITLE_FIELD = 'title';
    const DESCRIPTION_FIELD = 'description';
    const CATEGORY_FIELD = 'category';
    const TAGS_FIELD = 'tags';
    const TYPE_FIELD = 'type';
    const TECHNOLOGIES_FIELD = 'technologies';
    const DYNAMIC_CONTENT_CONFIG_FIELD = 'dynamic_content_config';
    const STATIC_CONTENT_CONFIG_FIELD = 'static_content_config';
    const SERIALIZED_DYNAMIC_CONTENT_CONFIG_GRAPHQL = 'serialized_dynamic_content_config';
    const SERIALIZED_STATIC_CONTENT_CONFIG_GRAPHQL = 'serialized_static_content_config';
    const CREATED_AT_FIELD = 'created_at';
    const UPDATED_AT_FIELD = 'updated_at';
    const AUTHOR_FIELD = 'author';
    const IMAGE_FIELD = 'image';

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
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return __($this->get(self::TITLE_FIELD), 'builderius');
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        $this->set(self::TITLE_FIELD, sanitize_text_field($title));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return __($this->get(self::DESCRIPTION_FIELD), 'builderius');
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        $this->set(self::DESCRIPTION_FIELD, sanitize_text_field($description));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return $this->get(self::CATEGORY_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setCategory($category)
    {
        $this->set(self::CATEGORY_FIELD, $category);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTags()
    {
        return $this->get(self::TAGS_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setTags(array $tags)
    {
        $this->set(self::TAGS_FIELD, $tags);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTag($tag)
    {
        $tags = $this->getTags();
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->setTags($tags);
        }

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
    public function getTechnologies()
    {
        return $this->get(self::TECHNOLOGIES_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setTechnologies(array $technologies)
    {
        $this->set(self::TECHNOLOGIES_FIELD, $technologies);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTechnology($technology)
    {
        $technologies = $this->getTechnologies();
        if (!in_array($technology, $technologies)) {
            $technologies[] = $technology;
            $this->set(self::TECHNOLOGIES_FIELD, $technologies);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDynamicContentConfig()
    {
        return $this->get(self::DYNAMIC_CONTENT_CONFIG_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setDynamicContentConfig(array $contentConfig)
    {
        $this->set(self::DYNAMIC_CONTENT_CONFIG_FIELD, $contentConfig);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStaticContentConfig()
    {
        return $this->get(self::STATIC_CONTENT_CONFIG_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setStaticContentConfig(array $contentConfig)
    {
        $this->set(self::STATIC_CONTENT_CONFIG_FIELD, $contentConfig);

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
    public function getUpdatedAt()
    {
        return $this->get(self::UPDATED_AT_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->set(self::UPDATED_AT_FIELD, $updatedAt);

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
    public function getImage()
    {
        return $this->get(self::IMAGE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setImage($image)
    {
        $this->set(self::IMAGE_FIELD, $image);

        return $this;
    }
}