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
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
class FilteredValidationException extends \Builderius\Respect\Validation\Exceptions\ValidationException
{
    public const EXTRA = 'extra';
    /**
     * {@inheritDoc}
     */
    protected function chooseTemplate() : string
    {
        return $this->getParam('additionalChars') ? self::EXTRA : self::STANDARD;
    }
}
