<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;
use Builderius\Respect\Validation\Rules\Number;

class NumberType extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        return (new Number())->validate($input);
    }
}