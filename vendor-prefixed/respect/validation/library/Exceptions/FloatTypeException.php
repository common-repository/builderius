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
namespace Builderius\Respect\Validation\Exceptions;

/**
 * Exception class for FloatType rule.
 *
 * @author Henrique Moody <henriquemoody@gmail.com>
 * @author Reginaldo Junior <76regi@gmail.com>
 */
final class FloatTypeException extends \Builderius\Respect\Validation\Exceptions\ValidationException
{
    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [self::MODE_DEFAULT => [self::STANDARD => '{{name}} must be of type float'], self::MODE_NEGATIVE => [self::STANDARD => '{{name}} must not be of type float']];
}
