<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

/**
export type InputType =
 | ScalarType
 | EnumType
 | InputObjectType
 | ListOfType<InputType>
 | NonNull<
     | ScalarType
     | EnumType
     | InputObjectType
     | ListOfType<InputType>,
   >;
*/
interface InputType
{
}
