<?php

namespace Builderius\Bundle\SettingBundle\Validation\Exception;

use Builderius\Respect\Validation\Exceptions\ValidationException;

final class StripTagsException extends ValidationException
{
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD =>
                'Only {{allowedTags}} tags allowed in {{name}}',
        ]
    ];
}