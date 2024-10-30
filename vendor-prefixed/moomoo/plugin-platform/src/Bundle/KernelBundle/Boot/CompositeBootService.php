<?php

namespace Builderius\MooMoo\Platform\Bundle\KernelBundle\Boot;

use Builderius\Symfony\Component\DependencyInjection\ContainerInterface;
class CompositeBootService implements \Builderius\MooMoo\Platform\Bundle\KernelBundle\Boot\BootServiceInterface
{
    const TAG = 'moomoo_boot_service';
    /**
     * @var BootServiceInterface[]
     */
    private $services = [];
    /**
     * @param BootServiceInterface $service
     */
    public function addService(\Builderius\MooMoo\Platform\Bundle\KernelBundle\Boot\BootServiceInterface $service)
    {
        $this->services[] = $service;
    }
    /**
     * @inheritDoc
     */
    public function boot(\Builderius\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        foreach ($this->services as $service) {
            $service->boot($container);
        }
    }
}
