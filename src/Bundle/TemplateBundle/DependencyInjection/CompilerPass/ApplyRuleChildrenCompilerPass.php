<?php

namespace Builderius\Bundle\TemplateBundle\DependencyInjection\CompilerPass;

use Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\DependencyInjection\Definition;
use Builderius\Symfony\Component\DependencyInjection\Reference;

class ApplyRuleChildrenCompilerPass implements CompilerPassInterface
{
    const RULE_TAG = 'builderius_template_apply_rule';

    /**
     * @var array
     */
    private $rules;

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $rules = $container->findTaggedServiceIds(self::RULE_TAG);
        if (!$rules) {
            return;
        }

        $children = [];
        foreach ($rules as $rule => $attributes) {
            $ruleDefinition = $container->getDefinition($rule);
            $arguments = $ruleDefinition->getArguments()[0];
            if (isset($arguments['parent']) && $arguments['parent'] !== null && is_string($arguments['parent'])) {
                $children[$arguments['parent']][$arguments['name']] = new Reference($rule);
            }
            $this->rules[$arguments['name']] = $ruleDefinition;
        }
        /** @var Definition $definition */
        foreach ($this->rules as $name => $definition) {
            if (isset($children[$name])) {
                $args = $definition->getArguments()[0];
                $args['children'] = $children[$name];
                $definition->setArgument(0, $args);
            }
        }
    }
}
