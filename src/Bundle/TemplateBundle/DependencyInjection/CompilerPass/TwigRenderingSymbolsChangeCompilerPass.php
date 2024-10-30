<?php

namespace Builderius\Bundle\TemplateBundle\DependencyInjection\CompilerPass;

use Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\DependencyInjection\Reference;

class TwigRenderingSymbolsChangeCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $twig = $container->getDefinition('twig');
        if ($twig) {
            $twig->addMethodCall('setLexer', [new Reference('builderius_template.twig.lexer')]);
        }
    }
}