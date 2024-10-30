<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

interface WrappingType
{
    public function getWrappedType(bool $recurse = \false) : \Builderius\GraphQL\Type\Definition\Type;
}
