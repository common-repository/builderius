<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class ObjectType extends AbstractRule
{
    /**
     * {@inheritDoc}
     */
    public function validate($input) : bool
    {
        return is_object($input) || (is_array($input) && count(array_filter(array_keys($input), 'is_string')) > 0) ;
    }
}
