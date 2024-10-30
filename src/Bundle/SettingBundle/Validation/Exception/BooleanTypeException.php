<?php

namespace Builderius\Bundle\SettingBundle\Validation\Exception;

use Builderius\Respect\Validation\Exceptions\ValidationException;

final class BooleanTypeException extends ValidationException
{
    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must be of type boolean'
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must not be of type boolean'
        ]
    ];
}
