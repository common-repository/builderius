<?php

namespace Builderius\MooMoo\Platform\Bundle\ConditionBundle;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\DependencyInjection\CompilerPass\ConditionsNamesServicesCompilerPass;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
class ConditionBundle extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle
{
    /**
     * @inheritDoc
     */
    public function build(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_condition', 'moomoo_condition.registry.conditions', 'addCondition'));
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\ConditionBundle\DependencyInjection\CompilerPass\ConditionsNamesServicesCompilerPass());
    }
}
