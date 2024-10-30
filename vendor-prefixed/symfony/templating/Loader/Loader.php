<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\Templating\Loader;

use Builderius\Psr\Log\LoggerInterface;
/**
 * Loader is the base class for all template loader classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class Loader implements \Builderius\Symfony\Component\Templating\Loader\LoaderInterface
{
    /**
     * @var LoggerInterface|null
     */
    protected $logger;
    /**
     * Sets the debug logger to use for this loader.
     */
    public function setLogger(\Builderius\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
