<?php

namespace Builderius\Bundle\TemplateBundle\Converter\Version\v0_9_9_5;

use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;

class BuilderiusTemplateTypeChangingConverter implements BuilderiusTemplateConfigVersionConverterInterface
{
    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        return '0.9.9.5';
    }

    /**
     * @inheritDoc
     */
    public function convert(array $config)
    {
        if (
            isset($config['template']) &&
            isset($config['template']['type']) &&
            in_array($config['template']['type'], ['singular', 'collection', 'other'])
        ) {
            $config['template']['type'] = 'template';
        }

        return $config;
    }
}