<?php

namespace Builderius\Bundle\ResponsiveBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;

class BuilderiusResponsiveBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_responsive_strategy',
                'builderius_responsive.provider.responsive_strategies',
                'addResponsiveStrategy'
            )
        );
    }
}
