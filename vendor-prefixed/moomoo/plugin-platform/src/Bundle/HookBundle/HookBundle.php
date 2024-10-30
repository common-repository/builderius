<?php

namespace Builderius\MooMoo\Platform\Bundle\HookBundle;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Registrator\HooksRegistratorInterface;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Registry\HooksRegistryInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
class HookBundle extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle
{
    /**
     * @inheritDoc
     */
    public function build(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_hook', 'moomoo_hook.registry.hooks', 'addHook'));
    }
    /**
     * @inheritDoc
     */
    public function boot()
    {
        /** @var HooksRegistryInterface $hooksRegistry */
        $hooksRegistry = $this->container->get('moomoo_hook.registry.hooks');
        /** @var HooksRegistratorInterface $hooksRegistrator */
        $hooksRegistrator = $this->container->get('moomoo_hook.hooks_registrator.main');
        $hooksRegistrator->registerHooks($hooksRegistry->getHooks());
        parent::boot();
    }
}
