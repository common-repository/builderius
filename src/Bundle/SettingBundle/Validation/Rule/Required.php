<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class Required extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        if (null === $input) {
            return false;
        } elseif (is_string($input) && empty(trim($input))) {
            return false;
        } elseif (is_array($input) && empty($input)) {
            return false;
        }

        return true;
    }
}