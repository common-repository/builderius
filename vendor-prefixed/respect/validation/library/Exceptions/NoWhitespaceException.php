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
 * @author Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 * @author Danilo Benevides <danilobenevides01@gmail.com>
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
final class NoWhitespaceException extends \Builderius\Respect\Validation\Exceptions\ValidationException
{
    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [self::MODE_DEFAULT => [self::STANDARD => '{{name}} must not contain whitespace'], self::MODE_NEGATIVE => [self::STANDARD => '{{name}} must contain whitespace']];
}
