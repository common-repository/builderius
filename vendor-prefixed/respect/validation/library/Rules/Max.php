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

/**
 * Validates whether the input is less than or equal to a value.
 *
 * @author Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
final class Max extends \Builderius\Respect\Validation\Rules\AbstractComparison
{
    /**
     * {@inheritDoc}
     */
    protected function compare($left, $right) : bool
    {
        return $left <= $right;
    }
}