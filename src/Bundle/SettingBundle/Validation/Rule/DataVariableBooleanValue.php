<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class DataVariableBooleanValue extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        if (in_array($input['a1'], ['boolean']) && $input['c1'] !== null) {
            return is_bool($input['c1']);
        }

        return true;
    }
}