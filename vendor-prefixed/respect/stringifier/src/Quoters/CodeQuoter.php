<?php

/*
 * This file is part of Respect/Stringifier.
 *
 * (c) Henrique Moody <henriquemoody@gmail.com>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */
declare (strict_types=1);
namespace Builderius\Respect\Stringifier\Quoters;

use Builderius\Respect\Stringifier\Quoter;
/**
 * Add "`" quotes around a string depending on its level.
 *
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
final class CodeQuoter implements \Builderius\Respect\Stringifier\Quoter
{
    /**
     * {@inheritdoc}
     */
    public function quote(string $string, int $depth) : string
    {
        if (0 === $depth) {
            return \sprintf('`%s`', $string);
        }
        return $string;
    }
}
