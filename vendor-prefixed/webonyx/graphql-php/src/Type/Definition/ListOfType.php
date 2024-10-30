<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

use Builderius\GraphQL\Type\Schema;
use function is_callable;
class ListOfType extends \Builderius\GraphQL\Type\Definition\Type implements \Builderius\GraphQL\Type\Definition\WrappingType, \Builderius\GraphQL\Type\Definition\OutputType, \Builderius\GraphQL\Type\Definition\NullableType, \Builderius\GraphQL\Type\Definition\InputType
{
    /** @var callable():Type|Type */
    public $ofType;
    /**
     * @param callable():Type|Type $type
     */
    public function __construct($type)
    {
        $this->ofType = \is_callable($type) ? $type : \Builderius\GraphQL\Type\Definition\Type::assertType($type);
    }
    public function toString() : string
    {
        return '[' . $this->getOfType()->toString() . ']';
    }
    public function getOfType()
    {
        return \Builderius\GraphQL\Type\Schema::resolveType($this->ofType);
    }
    /**
     * @return ObjectType|InterfaceType|UnionType|ScalarType|InputObjectType|EnumType|(Type&WrappingType)
     */
    public function getWrappedType(bool $recurse = \false) : \Builderius\GraphQL\Type\Definition\Type
    {
        $type = $this->getOfType();
        return $recurse && $type instanceof \Builderius\GraphQL\Type\Definition\WrappingType ? $type->getWrappedType($recurse) : $type;
    }
}
