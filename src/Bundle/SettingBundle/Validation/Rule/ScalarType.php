<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class ScalarType extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        return is_scalar($input);
    }
}