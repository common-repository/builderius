<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class DataVariableFloatValue extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        if (in_array($input['a1'], ['float']) && $input['c1'] !== null) {
            return is_float($input['c1']) || is_int($input['c1']);
        }

        return true;
    }
}