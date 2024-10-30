<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;
use Builderius\Respect\Validation\Rules\Base64;
use Builderius\Respect\Validation\Rules\Url;

class Image extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        return (new Url())->validate(filter_var($input, FILTER_SANITIZE_URL)) ||
            (new Base64())->validate(preg_replace('#^data:image/(.*?);base64,#i', '$2', $input)) ||
            (new Svg())->validate($input);
    }
}