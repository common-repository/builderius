<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

class SvgOrDynamic extends AbstractBuilderiusDynamicDataAwareRule
{
    /**
     * @inheritDoc
     */
    protected function validateStatic($input): bool
    {
        return (new Svg())->validate($input);
    }
}