<?php

namespace Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection;

use Builderius\Symfony\Component\Config\FileLocator;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\DependencyInjection\Extension\Extension;
use Builderius\Symfony\Component\DependencyInjection\Loader;
class KernelExtension extends \Builderius\Symfony\Component\DependencyInjection\Extension\Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, \Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $loader = new \Builderius\Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, new \Builderius\Symfony\Component\Config\FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
