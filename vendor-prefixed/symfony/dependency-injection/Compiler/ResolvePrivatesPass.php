<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\DependencyInjection\Compiler;

use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ResolvePrivatesPass implements \Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            if ($definition->isPrivate()) {
                $definition->setPublic(\false);
                $definition->setPrivate(\true);
            }
        }
        foreach ($container->getAliases() as $id => $alias) {
            if ($alias->isPrivate()) {
                $alias->setPublic(\false);
                $alias->setPrivate(\true);
            }
        }
    }
}
