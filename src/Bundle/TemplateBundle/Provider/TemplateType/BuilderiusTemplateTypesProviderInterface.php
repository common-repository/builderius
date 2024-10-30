<?php

namespace Builderius\Bundle\TemplateBundle\Provider\TemplateType;

use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateTechnologyInterface;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateTypeInterface;

interface BuilderiusTemplateTypesProviderInterface
{
    /**
     * @return BuilderiusTemplateTypeInterface[]
     */
    public function getTypes();

    /**
     * @param string $name
     * @return BuilderiusTemplateTypeInterface|null
     */
    public function getType($name);

    /**
     * @param string $name
     * @return boolean
     */
    public function hasType($name);

    /**
     * @param string $technology
     * @return BuilderiusTemplateTypeInterface[]
     */
    public function getTypesWithTechnology($technology);

    /**
     * @return BuilderiusTemplateTechnologyInterface[]
     */
    public function getTechnologies();
}