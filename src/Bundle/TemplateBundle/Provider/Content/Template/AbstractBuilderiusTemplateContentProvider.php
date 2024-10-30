<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Content\Template;

abstract class AbstractBuilderiusTemplateContentProvider implements BuilderiusTemplateContentProviderInterface
{
    /**
     * @var array
     */
    protected $technologies = [];

    /**
     * @inheritDoc
     */
    public function getTechnologies()
    {
        return $this->technologies;
    }

    /**
     * @inheritDoc
     */
    public function setTechnologies(array $technologies)
    {
        $this->technologies = $technologies;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTechnology($technology)
    {
        if (!in_array($technology, $this->technologies)) {
            $this->technologies[] = $technology;
        }

        return $this;
    }
}