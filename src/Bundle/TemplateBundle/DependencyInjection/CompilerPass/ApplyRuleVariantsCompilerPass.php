<?php

namespace Builderius\Bundle\TemplateBundle\DependencyInjection\CompilerPass;

use Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\DependencyInjection\Reference;

class ApplyRuleVariantsCompilerPass implements CompilerPassInterface
{
    const RULE_VARIANT_TAG = 'builderius_template_apply_rule_variant';
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
        foreach ($rules as $rule => $attributes) {
            $this->rules[$container->getDefinition($rule)->getArgument(0)['name']] = $rule;
        }

        $variants = $container->findTaggedServiceIds(self::RULE_VARIANT_TAG);
        if ($variants === null) {
            return;
        }

        foreach ($variants as $variant => $attributes) {
            $variantRule = null;
            $definition = $container->getDefinition($variant);
            $argumentsSet = $definition->getArguments();
            foreach ($argumentsSet as $arguments)
            if (isset($arguments['rule'])) {
                $variantRule = $arguments['rule'];
                break;
            }
            if ($variantRule === null) {
                $methodCallsSet = $definition->getMethodCalls();
                foreach ($methodCallsSet as $methodCall) {
                    if ($methodCall[0] === 'setRule') {
                        $variantRule = $methodCall[1][0];
                        break;
                    }
                }
            }
            if ($variantRule !== null) {
                if (isset($this->rules[$variantRule])) {
                    $ruleDefinition = $container->getDefinition($this->rules[$variantRule]);
                    $ruleDefinition->addMethodCall('addVariant', [new Reference($variant)]);
                }
            }
        }
    }
}
