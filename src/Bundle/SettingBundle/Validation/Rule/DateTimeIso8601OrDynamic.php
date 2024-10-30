<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

class DateTimeIso8601OrDynamic extends AbstractBuilderiusDynamicDataAwareRule
{
    /**
     * @inheritDoc
     */
    protected function validateStatic($input): bool
    {
        return (new DateTimeIso8601())->validate($input);
    }
}