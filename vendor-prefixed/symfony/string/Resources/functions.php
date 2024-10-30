<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\String;

/**
 * @experimental in 5.0
 */
function u(string $string = '') : \Builderius\Symfony\Component\String\UnicodeString
{
    return new \Builderius\Symfony\Component\String\UnicodeString($string);
}
/**
 * @experimental in 5.0
 */
function b(string $string = '') : \Builderius\Symfony\Component\String\ByteString
{
    return new \Builderius\Symfony\Component\String\ByteString($string);
}
