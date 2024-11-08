<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\Config\Loader;

use Builderius\Symfony\Component\Config\Exception\LoaderLoadException;
/**
 * DelegatingLoader delegates loading to other loaders using a loader resolver.
 *
 * This loader acts as an array of LoaderInterface objects - each having
 * a chance to load a given resource (handled by the resolver)
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DelegatingLoader extends \Builderius\Symfony\Component\Config\Loader\Loader
{
    public function __construct(\Builderius\Symfony\Component\Config\Loader\LoaderResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }
    /**
     * {@inheritdoc}
     */
    public function load($resource, string $type = null)
    {
        if (\false === ($loader = $this->resolver->resolve($resource, $type))) {
            throw new \Builderius\Symfony\Component\Config\Exception\LoaderLoadException($resource, null, null, null, $type);
        }
        return $loader->load($resource, $type);
    }
    /**
     * {@inheritdoc}
     */
    public function supports($resource, string $type = null)
    {
        return \false !== $this->resolver->resolve($resource, $type);
    }
}
