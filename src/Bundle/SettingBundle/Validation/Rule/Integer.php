<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;
use Builderius\Respect\Validation\Rules\IntVal;

class Integer extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        return (new IntVal())->validate($input);
    }
}