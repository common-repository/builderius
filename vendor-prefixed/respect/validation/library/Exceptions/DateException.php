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
 * @author Bruno Luiz da Silva <contato@brunoluiz.net>
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
final class DateException extends \Builderius\Respect\Validation\Exceptions\ValidationException
{
    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [self::MODE_DEFAULT => [self::STANDARD => '{{name}} must be a valid date in the format {{sample}}'], self::MODE_NEGATIVE => [self::STANDARD => '{{name}} must not be a valid date in the format {{sample}}']];
}