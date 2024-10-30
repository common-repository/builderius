<?php

namespace Builderius\Bundle\SettingBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;

class BuilderiusSettingBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_setting',
                'builderius_setting.registry.settings',
                'addSetting'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_setting_component',
                'builderius_setting.registry.settings_components',
                'addComponent'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_global_setting_value_generator',
                'builderius_setting.registry.global_setting_value_generators',
                'addGenerator'
            )
        );
    }
}
