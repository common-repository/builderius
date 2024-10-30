<?php

namespace Builderius\Bundle\LayoutBundle\Model;

class BuilderiusLayoutDecorator implements BuilderiusLayoutInterface
{
    /**
     * @var BuilderiusLayoutInterface
     */
    protected $layout;

    /**
     * @param BuilderiusLayoutInterface $layout
     */
    public function __construct(BuilderiusLayoutInterface $layout)
    {
        $this->layout = $layout;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->layout->getName();
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->layout->setName($name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->layout->getLabel();
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        $this->layout->setLabel($label);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->layout->getDescription();
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        $this->layout->setDescription($description);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTags()
    {
        return $this->layout->getTags();
    }

    /**
     * @inheritDoc
     */
    public function setTags(array $tags)
    {
        $this->layout->setTags($tags);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTag($tag)
    {
        return $this->layout->addTag($tag);
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return $this->layout->getConfig();
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $config)
    {
        $this->layout->setConfig($config);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return $this->layout->getCategory();
    }

    /**
     * @inheritDoc
     */
    public function setCategory($category)
    {
        $this->layout->setCategory($category);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getImage()
    {
        return $this->layout->getImage();
    }

    /**
     * @inheritDoc
     */
    public function setImage($image)
    {
        $this->layout->setImage($image);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTechnologies()
    {
        return $this->layout->getTechnologies();
    }

    /**
     * @inheritDoc
     */
    public function setTechnologies(array $technologies)
    {
        $this->layout->setTechnologies($technologies);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTechnology($technology)
    {
        $this->layout->addTechnology($technology);

        return $this;
    }
}