<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

/**
export type AbstractType =
InterfaceType |
UnionType;
*/
interface AbstractType
{
    /**
     * Resolves concrete ObjectType for given object value
     *
     * @param object  $objectValue
     * @param mixed[] $context
     *
     * @return mixed
     */
    public function resolveType($objectValue, $context, \Builderius\GraphQL\Type\Definition\ResolveInfo $info);
}
