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

use Builderius\Respect\Validation\Helpers\CanValidateIterable;
/**
 * Validates whether the pseudo-type of the input is iterable or not.
 *
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
final class IterableType extends \Builderius\Respect\Validation\Rules\AbstractRule
{
    use CanValidateIterable;
    /**
     * {@inheritDoc}
     */
    public function validate($input) : bool
    {
        return $this->isIterable($input);
    }
}