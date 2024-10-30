<?php

namespace Builderius\Bundle\LayoutBundle\DependencyInjection;

use Builderius\Symfony\Component\Config\FileLocator;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\DependencyInjection\Extension\Extension;
use Builderius\Symfony\Component\DependencyInjection\Loader;

class BuilderiusLayoutExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('layout_content_1.yml');
        $loader->load('layout_content_2.yml');
        $loader->load('layout_content_3.yml');
        $loader->load('layout_content_4.yml');
        $loader->load('layout_content_5.yml');
        $loader->load('layout_content_6.yml');
        $loader->load('layout_content_7.yml');
        $loader->load('layout_footer_1.yml');
        $loader->load('layout_footer_2.yml');
        $loader->load('layout_form_1.yml');
        $loader->load('layout_header_1.yml');
        $loader->load('layout_header_2.yml');
        $loader->load('layout_header_3.yml');
        $loader->load('layout_hero_1.yml');
        $loader->load('layout_hero_2.yml');
        $loader->load('layout_hero_3.yml');
        $loader->load('layout_hero_4.yml');
        $loader->load('layout_our_partners_1.yml');
        $loader->load('layout_subscribe_1.yml');
    }
}
