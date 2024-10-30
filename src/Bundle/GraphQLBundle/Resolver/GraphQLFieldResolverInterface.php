<?php

namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

interface GraphQLFieldResolverInterface
{
    /**
     * @return array
     */
    public function getTypeNames();

    /**
     * @return string
     */
    public function getFieldName();

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param mixed $objectValue
     * @param array $args
     * @param $context
     * @param ResolveInfo $info
     * @return bool
     */
    public function isApplicable($objectValue, array $args, $context, ResolveInfo $info);

    /**
     * @param mixed $objectValue
     * @param array $args
     * @param $context
     * @param ResolveInfo $info
     * @return mixed
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info);
}