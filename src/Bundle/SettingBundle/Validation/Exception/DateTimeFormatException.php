<?php

namespace Builderius\Bundle\SettingBundle\Validation\Exception;

use Builderius\Respect\Validation\Exceptions\ValidationException;

final class DateTimeFormatException extends ValidationException
{
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD =>
                "Date/time format can have just: \r\n
                Day Symbols - d, D, j, l, N, S, w, z \r\n
                Week Symbols - W \r\n
                Month Symbols - F, m, M, n, t \r\n
                Year Symbosl - L, o, Y, y \r\n
                Timezone Symbols - e, I, O, P, T, Z \r\n
                Full Date/Time Symbols - c, r, U \r\n
                Special Symbols - ':', ';', ',', '.', '/', '|', '-', '_', ' '",
        ]
    ];
}