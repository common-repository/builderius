<?php

namespace Builderius\Bundle\DeliverableBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;

class BuilderiusDeliverableBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_deliverable_provider',
                'builderius_deliverable.provider.deliverable.composite',
                'addProvider'
            )
        );
    }
}
