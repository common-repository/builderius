<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\Filesystem\Exception;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
class InvalidArgumentException extends \InvalidArgumentException implements \Builderius\Symfony\Component\Filesystem\Exception\ExceptionInterface
{
}