<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class StringType extends AbstractRule
{
    /**
     * {@inheritDoc}
     */
    public function validate($input) : bool
    {
        if (!is_string($input) && !is_int($input)) {
            return false;
        } else {
            $input = trim(preg_replace('/\\s+/', ' ', $input));

            return (esc_html($input) === sanitize_text_field(esc_html($input))) || (new Image())->validate($input);
        }
    }
}
