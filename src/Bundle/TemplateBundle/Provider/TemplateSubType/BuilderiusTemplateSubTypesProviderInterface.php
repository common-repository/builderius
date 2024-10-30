<?php

namespace Builderius\Bundle\TemplateBundle\Provider\TemplateSubType;

use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateSubTypeInterface;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateTypeInterface;

interface BuilderiusTemplateSubTypesProviderInterface
{
    /**
     * @return BuilderiusTemplateSubTypeInterface[]
     */
    public function getSubTypes($type);

    /**
     * @param string $name
     * @return BuilderiusTemplateSubTypeInterface|null
     */
    public function getSubType($type, $name);

    /**
     * @param string $name
     * @return boolean
     */
    public function hasSubType($type, $name);
}