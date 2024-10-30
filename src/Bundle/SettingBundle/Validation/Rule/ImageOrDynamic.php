<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

class ImageOrDynamic extends AbstractBuilderiusDynamicDataAwareRule
{
    /**
     * @inheritDoc
     */
    protected function validateStatic($input): bool
    {
        return (new Image())->validate($input);
    }
}