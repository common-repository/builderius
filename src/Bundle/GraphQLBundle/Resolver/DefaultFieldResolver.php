<?php

namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\PropertyAccess\PropertyAccess;

class DefaultFieldResolver
{
    /**
     * @inheritDoc
     */
    public static function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $fieldName = $info->fieldName;
        $property = null;
        if (\is_array($objectValue) || $objectValue instanceof \ArrayAccess) {
            if (isset($objectValue[$fieldName])) {
                $property = $objectValue[$fieldName];
            }
        } elseif (\is_object($objectValue)) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $property = $propertyAccessor->getValue($objectValue, $fieldName);
        }
        return $property instanceof \Closure ? $property($objectValue, $args, $context, $info) : $property;
    }
}