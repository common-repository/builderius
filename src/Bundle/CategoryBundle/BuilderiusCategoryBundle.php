<?php

namespace Builderius\Bundle\CategoryBundle;

use Builderius\Bundle\CategoryBundle\DependencyInjection\CompilerPass\TempCompatibilityCompilerPass;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;

class BuilderiusCategoryBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TempCompatibilityCompilerPass());
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_category',
                'builderius_category.provider.categories.standard',
                'addCategory'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_categories_provider',
                'builderius_category.provider.categories.composite',
                'addProvider'
            )
        );
    }
}
