<?php

namespace Builderius\Bundle\SettingBundle\Validation\Exception;

use Builderius\Respect\Validation\Exceptions\ValidationException;

final class PositiveException extends ValidationException
{
    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must be positive'
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must not be positive'
        ]
    ];
}
