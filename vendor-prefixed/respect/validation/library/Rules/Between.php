<?php

/*
 * This file is part of Respect/Validation.
 *
 * (c) Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */
declare (strict_types=1);
namespace Builderius\Respect\Validation\Rules;

use Builderius\Respect\Validation\Exceptions\ComponentException;
use Builderius\Respect\Validation\Helpers\CanCompareValues;
/**
 * Validates whether the input is between two other values.
 *
 * @author Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
final class Between extends \Builderius\Respect\Validation\Rules\AbstractEnvelope
{
    use CanCompareValues;
    /**
     * Initializes the rule.
     *
     * @param mixed $minValue
     * @param mixed $maxValue
     *
     * @throws ComponentException
     */
    public function __construct($minValue, $maxValue)
    {
        if ($this->toComparable($minValue) >= $this->toComparable($maxValue)) {
            throw new \Builderius\Respect\Validation\Exceptions\ComponentException('Minimum cannot be less than or equals to maximum');
        }
        parent::__construct(new \Builderius\Respect\Validation\Rules\AllOf(new \Builderius\Respect\Validation\Rules\Min($minValue), new \Builderius\Respect\Validation\Rules\Max($maxValue)), ['minValue' => $minValue, 'maxValue' => $maxValue]);
    }
}
