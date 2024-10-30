<?php

namespace Builderius\Bundle\ACFBundle\DependencyInjection;

use Builderius\Symfony\Component\Config\FileLocator;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\DependencyInjection\Extension\Extension;
use Builderius\Symfony\Component\DependencyInjection\Loader;

class BuilderiusACFExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('graphql_types.yml');
        $loader->load('graphql_resolvers.yml');
    }
}
