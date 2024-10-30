<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class MixedType extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        return true;
    }
}