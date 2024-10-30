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
 * Removes abstract Definitions.
 */
class RemoveAbstractDefinitionsPass implements \Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * Removes abstract definitions from the ContainerBuilder.
     */
    public function process(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            if ($definition->isAbstract()) {
                $container->removeDefinition($id);
                $container->log($this, \sprintf('Removed service "%s"; reason: abstract.', $id));
            }
        }
    }
}
