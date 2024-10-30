<?php

namespace Builderius\Bundle\TemplateBundle\Converter\Version;

interface BuilderiusTemplateConfigVersionConverterInterface
{
    /**
     * @return string
     */
    public function getVersion();

    /**
     * @param array $config
     * @return array
     * @throws \Exception
     */
    public function convert(array $config);
}