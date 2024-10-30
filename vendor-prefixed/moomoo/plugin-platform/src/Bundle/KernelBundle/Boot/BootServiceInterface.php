<?php

namespace Builderius\MooMoo\Platform\Bundle\KernelBundle\Boot;

use Builderius\Symfony\Component\DependencyInjection\ContainerInterface;
interface BootServiceInterface
{
    /**
     * @param ContainerInterface $container
     */
    public function boot(\Builderius\Symfony\Component\DependencyInjection\ContainerInterface $container);
}
