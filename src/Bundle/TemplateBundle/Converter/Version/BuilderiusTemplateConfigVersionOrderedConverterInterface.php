<?php

namespace Builderius\Bundle\TemplateBundle\Converter\Version;

interface BuilderiusTemplateConfigVersionOrderedConverterInterface
{
    /**
     * @return int
     */
    public function getOrder();
}