<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

use Builderius\GraphQL\Error\InvariantViolation;
use Builderius\GraphQL\Type\Schema;
use function is_callable;
class NonNull extends \Builderius\GraphQL\Type\Definition\Type implements \Builderius\GraphQL\Type\Definition\WrappingType, \Builderius\GraphQL\Type\Definition\OutputType, \Builderius\GraphQL\Type\Definition\InputType
{
    /** @var callable|(NullableType&Type) */
    private $ofType;
    /**
     * code sniffer doesn't understand this syntax. Pr with a fix here: waiting on https://github.com/squizlabs/PHP_CodeSniffer/pull/2919
     * phpcs:disable Squiz.Commenting.FunctionComment.SpacingAfterParamType
     * @param  (NullableType&Type)|callable $type
     */
    public function __construct($type)
    {
        /** @var Type&NullableType $nullableType*/
        $nullableType = $type;
        $this->ofType = $nullableType;
    }
    public function toString() : string
    {
        return $this->getWrappedType()->toString() . '!';
    }
    public function getOfType()
    {
        return \Builderius\GraphQL\Type\Schema::resolveType($this->ofType);
    }
    public function getWrappedType(bool $recurse = \false) : \Builderius\GraphQL\Type\Definition\Type
    {
        $type = $this->getOfType();
        return $recurse && $type instanceof \Builderius\GraphQL\Type\Definition\WrappingType ? $type->getWrappedType($recurse) : $type;
    }
}
