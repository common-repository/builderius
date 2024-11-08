<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Utils;

use Builderius\GraphQL\Type\Definition\AbstractType;
use Builderius\GraphQL\Type\Definition\CompositeType;
use Builderius\GraphQL\Type\Definition\ListOfType;
use Builderius\GraphQL\Type\Definition\NonNull;
use Builderius\GraphQL\Type\Definition\ObjectType;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Schema;
class TypeComparators
{
    /**
     * Provided two types, return true if the types are equal (invariant).
     *
     * @return bool
     */
    public static function isEqualType(\Builderius\GraphQL\Type\Definition\Type $typeA, \Builderius\GraphQL\Type\Definition\Type $typeB)
    {
        // Equivalent types are equal.
        if ($typeA === $typeB) {
            return \true;
        }
        // If either type is non-null, the other must also be non-null.
        if ($typeA instanceof \Builderius\GraphQL\Type\Definition\NonNull && $typeB instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
            return self::isEqualType($typeA->getWrappedType(), $typeB->getWrappedType());
        }
        // If either type is a list, the other must also be a list.
        if ($typeA instanceof \Builderius\GraphQL\Type\Definition\ListOfType && $typeB instanceof \Builderius\GraphQL\Type\Definition\ListOfType) {
            return self::isEqualType($typeA->getWrappedType(), $typeB->getWrappedType());
        }
        // Otherwise the types are not equal.
        return \false;
    }
    /**
     * Provided a type and a super type, return true if the first type is either
     * equal or a subset of the second super type (covariant).
     *
     * @return bool
     */
    public static function isTypeSubTypeOf(\Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Type\Definition\Type $maybeSubType, \Builderius\GraphQL\Type\Definition\Type $superType)
    {
        // Equivalent type is a valid subtype
        if ($maybeSubType === $superType) {
            return \true;
        }
        // If superType is non-null, maybeSubType must also be nullable.
        if ($superType instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
            if ($maybeSubType instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
                return self::isTypeSubTypeOf($schema, $maybeSubType->getWrappedType(), $superType->getWrappedType());
            }
            return \false;
        }
        if ($maybeSubType instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
            // If superType is nullable, maybeSubType may be non-null.
            return self::isTypeSubTypeOf($schema, $maybeSubType->getWrappedType(), $superType);
        }
        // If superType type is a list, maybeSubType type must also be a list.
        if ($superType instanceof \Builderius\GraphQL\Type\Definition\ListOfType) {
            if ($maybeSubType instanceof \Builderius\GraphQL\Type\Definition\ListOfType) {
                return self::isTypeSubTypeOf($schema, $maybeSubType->getWrappedType(), $superType->getWrappedType());
            }
            return \false;
        }
        if ($maybeSubType instanceof \Builderius\GraphQL\Type\Definition\ListOfType) {
            // If superType is not a list, maybeSubType must also be not a list.
            return \false;
        }
        // If superType type is an abstract type, maybeSubType type may be a currently
        // possible object type.
        return \Builderius\GraphQL\Type\Definition\Type::isAbstractType($superType) && $maybeSubType instanceof \Builderius\GraphQL\Type\Definition\ObjectType && $schema->isPossibleType($superType, $maybeSubType);
    }
    /**
     * Provided two composite types, determine if they "overlap". Two composite
     * types overlap when the Sets of possible concrete types for each intersect.
     *
     * This is often used to determine if a fragment of a given type could possibly
     * be visited in a context of another type.
     *
     * This function is commutative.
     *
     * @return bool
     */
    public static function doTypesOverlap(\Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Type\Definition\CompositeType $typeA, \Builderius\GraphQL\Type\Definition\CompositeType $typeB)
    {
        // Equivalent types overlap
        if ($typeA === $typeB) {
            return \true;
        }
        if ($typeA instanceof \Builderius\GraphQL\Type\Definition\AbstractType) {
            if ($typeB instanceof \Builderius\GraphQL\Type\Definition\AbstractType) {
                // If both types are abstract, then determine if there is any intersection
                // between possible concrete types of each.
                foreach ($schema->getPossibleTypes($typeA) as $type) {
                    if ($schema->isPossibleType($typeB, $type)) {
                        return \true;
                    }
                }
                return \false;
            }
            // Determine if the latter type is a possible concrete type of the former.
            return $schema->isPossibleType($typeA, $typeB);
        }
        if ($typeB instanceof \Builderius\GraphQL\Type\Definition\AbstractType) {
            // Determine if the former type is a possible concrete type of the latter.
            return $schema->isPossibleType($typeB, $typeA);
        }
        // Otherwise the types do not overlap.
        return \false;
    }
}
