<?php

namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

interface GraphQLTypeResolverInterface
{
    /**
     * @param mixed $value
     * @param mixed$context
     * @param ResolveInfo $info
     * @return mixed
     */
    public function resolve($value, $context, ResolveInfo $info);
}