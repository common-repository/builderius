<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Bridge\ProxyManager\LazyProxy\Instantiator;

use Builderius\ProxyManager\Factory\LazyLoadingValueHolderFactory as BaseFactory;
use Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface;
use Builderius\Symfony\Bridge\ProxyManager\LazyProxy\PhpDumper\LazyLoadingValueHolderGenerator;
/**
 * @internal
 */
class LazyLoadingValueHolderFactory extends \Builderius\ProxyManager\Factory\LazyLoadingValueHolderFactory
{
    private $generator;
    /**
     * {@inheritdoc}
     */
    public function getGenerator() : \Builderius\ProxyManager\ProxyGenerator\ProxyGeneratorInterface
    {
        return $this->generator ?: ($this->generator = new \Builderius\Symfony\Bridge\ProxyManager\LazyProxy\PhpDumper\LazyLoadingValueHolderGenerator());
    }
}
