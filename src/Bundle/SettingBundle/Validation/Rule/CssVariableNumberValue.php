<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class CssVariableNumberValue extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        if (in_array($input['a1'], ['number','percentage','length','angle','time']) && $input['b2'] !== null) {
            if (
                preg_match(
                    '/^(([0-9]*)|(([0-9]*)(?:.[0-9]+)*))$/',
                    $input['b2']
                )
            ) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }
}