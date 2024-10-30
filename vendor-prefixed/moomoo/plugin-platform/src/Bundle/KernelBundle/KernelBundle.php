<?php

namespace Builderius\MooMoo\Platform\Bundle\KernelBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Boot\BootServiceInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Boot\CompositeBootService;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
class KernelBundle extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle
{
    /**
     * @inheritDoc
     */
    public function build(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass(\Builderius\MooMoo\Platform\Bundle\KernelBundle\Boot\CompositeBootService::TAG, 'moomoo_kernel.boot_service.composite', 'addService'));
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('twig.extension', 'twig', 'addExtension'));
        $container->addCompilerPass(new \Builderius\Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass('event_dispatcher', 'moomoo_event_listener', 'moomoo_event_subscriber'));
    }
    /**
     * @inheritDoc
     */
    public function boot()
    {
        /** @var BootServiceInterface $compositeBoot */
        $compositeBoot = $this->container->get('moomoo_kernel.boot_service.composite');
        $compositeBoot->boot($this->container);
        parent::boot();
    }
}
