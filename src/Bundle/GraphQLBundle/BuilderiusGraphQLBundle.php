<?php

namespace Builderius\Bundle\GraphQLBundle;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;

class BuilderiusGraphQLBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_graphql_field_resolver',
                'builderius_graphql.provider.field_resolvers',
                'addResolver'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_graphql_types_provider',
                'builderius_graphql.provider.types.composite',
                'addProvider'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_graphql_type',
                'builderius_graphql.provider.types.standard',
                'addType'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_graphql_type_config',
                'builderius_graphql.provider.type_configs',
                'addTypeConfig'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_graphql_directive',
                'builderius_graphql.provider.directives',
                'addDirective'
            )
        );
    }
}
