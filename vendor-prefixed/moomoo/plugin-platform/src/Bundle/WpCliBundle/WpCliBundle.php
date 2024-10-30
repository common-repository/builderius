<?php

namespace Builderius\MooMoo\Platform\Bundle\WpCliBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\MooMoo\Platform\Bundle\WpCliBundle\Registrator\WpCliCommandsRegistratorInterface;
use Builderius\MooMoo\Platform\Bundle\WpCliBundle\Registry\WpCliCommandsRegistryInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
class WpCliBundle extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle
{
    /**
     * @inheritDoc
     */
    public function build(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        /** @var ContainerBuilder $container */
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_wpcli_command', 'moomoo_wpcli.registry.commands', 'addCommand'));
    }
    /**
     * @inheritDoc
     */
    public function boot()
    {
        /** @var WpCliCommandsRegistryInterface $commandsRegistry */
        $commandsRegistry = $this->container->get('moomoo_wpcli.registry.commands');
        /** @var WpCliCommandsRegistratorInterface $commandsRegistrator */
        $commandsRegistrator = $this->container->get('moomoo_wpcli.registrator.commands');
        $commandsRegistrator->registerCommands($commandsRegistry->getCommands());
        parent::boot();
    }
}
