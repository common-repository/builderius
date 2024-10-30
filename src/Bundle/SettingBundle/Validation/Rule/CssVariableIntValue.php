<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class CssVariableIntValue extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        if ($input['a1'] === 'integer' && $input['b2'] !== null) {
            if (
                preg_match(
                    '/^[0-9]*$/',
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