<?php

namespace Builderius\Bundle\GraphQLBundle\DependencyInjection;

use Builderius\Symfony\Component\Config\FileLocator;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\DependencyInjection\Extension\Extension;
use Builderius\Symfony\Component\DependencyInjection\Loader;

class BuilderiusGraphQLExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('resolvers.yml');
        $loader->load('types.yml');
        $loader->load('common_types_configs.yml');
        $loader->load('post_types_configs.yml');
        $loader->load('user_types_configs.yml');
        $loader->load('comment_types_configs.yml');
        $loader->load('featuredimage_types_configs.yml');
        $loader->load('superglobal_variable_configs.yml');
    }
}
