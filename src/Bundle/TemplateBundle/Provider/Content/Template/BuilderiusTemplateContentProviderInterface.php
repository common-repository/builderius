<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Content\Template;

interface BuilderiusTemplateContentProviderInterface
{
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
     * @param $technology
     * @return $this
     */
    public function addTechnology($technology);

    /**
     * @return string|null
     */
    public function getContentType();

    /**
     * @param string $technology
     * @param array $contentConfig
     * @return mixed
     * @throws \Exception
     */
    public function getContent($technology, array $contentConfig);
}