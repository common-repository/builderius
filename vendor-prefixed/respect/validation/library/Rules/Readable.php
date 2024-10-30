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

use Builderius\Psr\Http\Message\StreamInterface;
use SplFileInfo;
use function is_readable;
use function is_string;
/**
 * Validates if the given data is a file exists and is readable.
 *
 * @author Danilo Correa <danilosilva87@gmail.com>
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
final class Readable extends \Builderius\Respect\Validation\Rules\AbstractRule
{
    /**
     * {@inheritDoc}
     */
    public function validate($input) : bool
    {
        if ($input instanceof \SplFileInfo) {
            return $input->isReadable();
        }
        if ($input instanceof \Builderius\Psr\Http\Message\StreamInterface) {
            return $input->isReadable();
        }
        return \is_string($input) && \is_readable($input);
    }
}
