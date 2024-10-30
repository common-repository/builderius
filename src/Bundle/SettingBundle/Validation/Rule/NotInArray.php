<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

class NotInArray extends InArray
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        return parent::validate($input) !== true;
    }
}