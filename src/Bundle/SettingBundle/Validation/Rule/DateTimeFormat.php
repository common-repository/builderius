<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class DateTimeFormat extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        $regex = '/^[dDjlNSwzWFmMntLoYyaABgGhHisuveIOPTZcrU\:\;\,\.\/\|\-\_\ ]*$/';
        if (preg_match($regex, implode('', $input))) {
            return true;
        }

        return false;
    }
}