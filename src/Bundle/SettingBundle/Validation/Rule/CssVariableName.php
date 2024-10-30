<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class CssVariableName extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        if (preg_match('/^--[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*$/', $input)) {
            return true;
        }

        return false;
    }
}