<?php

namespace Builderius\Bundle\SettingBundle\Validation\Exception;

use Builderius\Respect\Validation\Exceptions\ValidationException;

final class ColorException extends ValidationException
{
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD =>
                'color must be HEX or RGB(A) or HSL(A) formatted value, or must be one of: currentcolor, inherit, initial, unset',
        ]
    ];
}