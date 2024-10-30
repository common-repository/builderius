<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\FragmentSpreadNode;
use Builderius\GraphQL\Language\AST\InlineFragmentNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Type\Definition\AbstractType;
use Builderius\GraphQL\Type\Definition\CompositeType;
use Builderius\GraphQL\Type\Definition\InterfaceType;
use Builderius\GraphQL\Type\Definition\ObjectType;
use Builderius\GraphQL\Type\Definition\UnionType;
use Builderius\GraphQL\Type\Schema;
use Builderius\GraphQL\Utils\TypeInfo;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
class PossibleFragmentSpreads extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        return [\Builderius\GraphQL\Language\AST\NodeKind::INLINE_FRAGMENT => function (\Builderius\GraphQL\Language\AST\InlineFragmentNode $node) use($context) : void {
            $fragType = $context->getType();
            $parentType = $context->getParentType();
            if (!$fragType instanceof \Builderius\GraphQL\Type\Definition\CompositeType || !$parentType instanceof \Builderius\GraphQL\Type\Definition\CompositeType || $this->doTypesOverlap($context->getSchema(), $fragType, $parentType)) {
                return;
            }
            $context->reportError(new \Builderius\GraphQL\Error\Error(self::typeIncompatibleAnonSpreadMessage($parentType, $fragType), [$node]));
        }, \Builderius\GraphQL\Language\AST\NodeKind::FRAGMENT_SPREAD => function (\Builderius\GraphQL\Language\AST\FragmentSpreadNode $node) use($context) : void {
            $fragName = $node->name->value;
            $fragType = $this->getFragmentType($context, $fragName);
            $parentType = $context->getParentType();
            if (!$fragType || !$parentType || $this->doTypesOverlap($context->getSchema(), $fragType, $parentType)) {
                return;
            }
            $context->reportError(new \Builderius\GraphQL\Error\Error(self::typeIncompatibleSpreadMessage($fragName, $parentType, $fragType), [$node]));
        }];
    }
    private function doTypesOverlap(\Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Type\Definition\CompositeType $fragType, \Builderius\GraphQL\Type\Definition\CompositeType $parentType)
    {
        // Checking in the order of the most frequently used scenarios:
        // Parent type === fragment type
        if ($parentType === $fragType) {
            return \true;
        }
        // Parent type is interface or union, fragment type is object type
        if ($parentType instanceof \Builderius\GraphQL\Type\Definition\AbstractType && $fragType instanceof \Builderius\GraphQL\Type\Definition\ObjectType) {
            return $schema->isPossibleType($parentType, $fragType);
        }
        // Parent type is object type, fragment type is interface (or rather rare - union)
        if ($parentType instanceof \Builderius\GraphQL\Type\Definition\ObjectType && $fragType instanceof \Builderius\GraphQL\Type\Definition\AbstractType) {
            return $schema->isPossibleType($fragType, $parentType);
        }
        // Both are object types:
        if ($parentType instanceof \Builderius\GraphQL\Type\Definition\ObjectType && $fragType instanceof \Builderius\GraphQL\Type\Definition\ObjectType) {
            return $parentType === $fragType;
        }
        // Both are interfaces
        // This case may be assumed valid only when implementations of two interfaces intersect
        // But we don't have information about all implementations at runtime
        // (getting this information via $schema->getPossibleTypes() requires scanning through whole schema
        // which is very costly to do at each request due to PHP "shared nothing" architecture)
        //
        // So in this case we just make it pass - invalid fragment spreads will be simply ignored during execution
        // See also https://github.com/webonyx/graphql-php/issues/69#issuecomment-283954602
        if ($parentType instanceof \Builderius\GraphQL\Type\Definition\InterfaceType && $fragType instanceof \Builderius\GraphQL\Type\Definition\InterfaceType) {
            return \true;
            // Note that there is one case when we do have information about all implementations:
            // When schema descriptor is defined ($schema->hasDescriptor())
            // BUT we must avoid situation when some query that worked in development had suddenly stopped
            // working in production. So staying consistent and always validate.
        }
        // Interface within union
        if ($parentType instanceof \Builderius\GraphQL\Type\Definition\UnionType && $fragType instanceof \Builderius\GraphQL\Type\Definition\InterfaceType) {
            foreach ($parentType->getTypes() as $type) {
                if ($type->implementsInterface($fragType)) {
                    return \true;
                }
            }
        }
        if ($parentType instanceof \Builderius\GraphQL\Type\Definition\InterfaceType && $fragType instanceof \Builderius\GraphQL\Type\Definition\UnionType) {
            foreach ($fragType->getTypes() as $type) {
                if ($type->implementsInterface($parentType)) {
                    return \true;
                }
            }
        }
        if ($parentType instanceof \Builderius\GraphQL\Type\Definition\UnionType && $fragType instanceof \Builderius\GraphQL\Type\Definition\UnionType) {
            foreach ($fragType->getTypes() as $type) {
                if ($parentType->isPossibleType($type)) {
                    return \true;
                }
            }
        }
        return \false;
    }
    public static function typeIncompatibleAnonSpreadMessage($parentType, $fragType)
    {
        return \sprintf('Fragment cannot be spread here as objects of type "%s" can never be of type "%s".', $parentType, $fragType);
    }
    private function getFragmentType(\Builderius\GraphQL\Validator\ValidationContext $context, $name)
    {
        $frag = $context->getFragment($name);
        if ($frag) {
            $type = \Builderius\GraphQL\Utils\TypeInfo::typeFromAST($context->getSchema(), $frag->typeCondition);
            if ($type instanceof \Builderius\GraphQL\Type\Definition\CompositeType) {
                return $type;
            }
        }
        return null;
    }
    public static function typeIncompatibleSpreadMessage($fragName, $parentType, $fragType)
    {
        return \sprintf('Fragment "%s" cannot be spread here as objects of type "%s" can never be of type "%s".', $fragName, $parentType, $fragType);
    }
}
