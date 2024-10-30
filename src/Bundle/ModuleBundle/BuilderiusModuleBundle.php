<?php

namespace Builderius\Bundle\ModuleBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;

class BuilderiusModuleBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_module',
                'builderius_module.provider.regular_modules',
                'addModule'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_composite_module',
                'builderius_module.provider.composite_modules.regular',
                'addModule'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_modules_provider',
                'builderius_module.provider.composite',
                'addProvider'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_composite_modules_provider',
                'builderius_module.provider.composite_modules',
                'addProvider'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_module_rendering_condition',
                'builderius_module.provider.rendering_conditions',
                'addRenderingCondition'
            )
        );
    }
}
