<?php

namespace Builderius\Bundle\TemplateBundle\DependencyInjection;

use Builderius\Symfony\Component\Config\FileLocator;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\DependencyInjection\Extension\Extension;
use Builderius\Symfony\Component\DependencyInjection\Loader;

class BuilderiusTemplateExtension extends Extension
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('apply_rules.yml');
        $loader->load('apply_rules_starters.yml');
        $loader->load('event_listeners.yml');
        $loader->load('template_types.yml');
        $loader->load('template_applicants.yml');
        $loader->load('version_converters.yml');
        $loader->load('graphql_types.yml');
        $loader->load('graphql_resolvers.yml');
        $loader->load('template_acceptable_hooks_providers.yml');
        $loader->load('template_acceptable_wp_hooks.yml');
        $loader->load('template_acceptable_generatepress_hooks.yml');
        $loader->load('template_acceptable_kadence_hooks.yml');
        $loader->load('template_acceptable_astra_hooks.yml');
        $loader->load('template_acceptable_blocksy_hooks.yml');
        $loader->load('dynamic_data_helpers.yml');
    }
}
