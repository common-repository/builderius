<?php

namespace Builderius\Bundle\SettingBundle\Validation\Exception;

use Builderius\Respect\Validation\Exceptions\ValidationException;

final class IntegerException extends ValidationException
{
    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must be of type integer'
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must not be of type integer'
        ]
    ];
}
