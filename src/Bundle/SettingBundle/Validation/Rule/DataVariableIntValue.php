<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class DataVariableIntValue extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        if ($input['a1'] === 'integer' && $input['c1'] !== null) {
            return is_int($input['c1']);
        }

        return true;
    }
}