<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class AttributeName extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        if (preg_match('/^[A-Za-z0-9][\w-]*(?!_)\w$/', $input)) {
            return true;
        }

        return false;
    }
}