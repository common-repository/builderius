<?php

namespace Builderius\MooMoo\Platform\Bundle\ConditionBundle\DependencyInjection\CompilerPass;

use Builderius\Symfony\Component\DependencyInjection\ChildDefinition;
use Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\DependencyInjection\Definition;
use Builderius\Symfony\Component\DependencyInjection\Reference;
class ConditionsNamesServicesCompilerPass implements \Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    const CONDITION_TAG = 'moomoo_condition';
    /**
     * @var array
     */
    private $conditions;
    /**
     * @inheritDoc
     */
    public function process(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $conditions = $container->findTaggedServiceIds(self::CONDITION_TAG);
        if (!$conditions) {
            return;
        }
        foreach ($conditions as $condition => $attributes) {
            $definition = $container->getDefinition($condition);
            $arguments = $definition->getArguments();
            if (!empty($arguments)) {
                $newDef = new \Builderius\Symfony\Component\DependencyInjection\ChildDefinition($condition);
                $container->setDefinition($arguments[0]['name'], $newDef);
            } else {
                $calls = $definition->getMethodCalls('setName');
                foreach ($calls as $call) {
                    if ($call[0] === 'setName') {
                        $newDef = new \Builderius\Symfony\Component\DependencyInjection\ChildDefinition($condition);
                        $container->setDefinition($call[1][0], $newDef);
                    }
                }
            }
        }
    }
}
