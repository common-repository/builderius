<?php

namespace Builderius\Bundle\BuilderBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;

class BuilderiusBuilderBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_builder_form_tab',
                'builderius_builder.registry.builder_form_tabs',
                'addTab'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_css_framework',
                'builderius_builder.registry.css_frameworks',
                'addFramework'
            )
        );
    }
}
