<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class DataVariableName extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
            if (preg_match('/^[a-z]+[a-zA-Z0-9]*$/', $input)) {
            return true;
        }

        return false;
    }
}