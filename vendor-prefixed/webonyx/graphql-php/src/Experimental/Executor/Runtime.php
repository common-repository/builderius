<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Experimental\Executor;

use Builderius\GraphQL\Language\AST\ValueNode;
use Builderius\GraphQL\Type\Definition\EnumType;
use Builderius\GraphQL\Type\Definition\InputObjectType;
use Builderius\GraphQL\Type\Definition\InputType;
use Builderius\GraphQL\Type\Definition\ListOfType;
use Builderius\GraphQL\Type\Definition\NonNull;
use Builderius\GraphQL\Type\Definition\ScalarType;
/**
 * @internal
 */
interface Runtime
{
    /**
     * @param ScalarType|EnumType|InputObjectType|ListOfType|NonNull $type
     */
    public function evaluate(\Builderius\GraphQL\Language\AST\ValueNode $valueNode, \Builderius\GraphQL\Type\Definition\InputType $type);
    public function addError($error);
}
