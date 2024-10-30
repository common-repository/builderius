<?php

namespace Builderius\MooMoo\Platform\Bundle\QueryBundle\DependencyInjection\CompilerPass;

use Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\DependencyInjection\Reference;
class QueryCompilerPass implements \Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        //$container->set('moomoo_query.wp_the_query', $GLOBALS['wp_the_query']);
        //$container->set('moomoo_query.wp_query', $GLOBALS['wp_query']);
    }
}
