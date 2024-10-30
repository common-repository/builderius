<?php

namespace Builderius\Bundle\LayoutBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;

class BuilderiusLayoutBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_layout',
                'builderius_layout.provider.standard',
                'addLayout'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_layout_provider',
                'builderius_layout.provider.composite',
                'addProvider'
            )
        );
    }
}
