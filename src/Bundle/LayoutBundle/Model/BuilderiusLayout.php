<?php

namespace Builderius\Bundle\LayoutBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusLayout extends ParameterBag implements BuilderiusLayoutInterface
{
    const NAME_FIELD = 'name';
    const LABEL_FIELD = 'label';
    const DESCRIPTION_FIELD = 'description';
    const TAGS_FIELD = 'tags';
    const CONFIG_FIELD = 'config';
    const CATEGORY_FIELD = 'category';
    const IMAGE_FIELD = 'image';
    const TECHNOLOGIES_FIELD = 'technologies';

    /**
     * {@inheritdoc}
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
    public function getLabel()
    {
        return __($this->get(self::LABEL_FIELD), 'builderius');
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        $this->set(self::LABEL_FIELD, $label);

        return $this;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->get(self::CONFIG_FIELD, []);
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $config)
    {
        $this->set(self::CONFIG_FIELD, $config);

        return $this;
    }

    /**
     * {@inheritdoc}
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
}
