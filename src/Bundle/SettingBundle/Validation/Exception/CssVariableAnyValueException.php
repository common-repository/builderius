<?php

namespace Builderius\Bundle\SettingBundle\Validation\Exception;

use Builderius\Respect\Validation\Exceptions\ValidationException;

final class CssVariableAnyValueException extends ValidationException
{
    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Any-value can contain a-z, A-Z, 0-9, "+", "-", "*", "/", "%", "(", ")", ".", ",", """, "_", "#". Q-ty of "(" should be equal to q-ty of ")"'
        ]
    ];
}
