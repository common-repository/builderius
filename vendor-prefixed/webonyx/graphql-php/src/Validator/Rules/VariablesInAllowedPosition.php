<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Language\AST\NullValueNode;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Language\AST\ValueNode;
use Builderius\GraphQL\Language\AST\VariableDefinitionNode;
use Builderius\GraphQL\Type\Definition\NonNull;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Schema;
use Builderius\GraphQL\Utils\TypeComparators;
use Builderius\GraphQL\Utils\TypeInfo;
use Builderius\GraphQL\Utils\Utils;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
class VariablesInAllowedPosition extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    /**
     * A map from variable names to their definition nodes.
     *
     * @var VariableDefinitionNode[]
     */
    public $varDefMap;
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        return [\Builderius\GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION => ['enter' => function () : void {
            $this->varDefMap = [];
        }, 'leave' => function (\Builderius\GraphQL\Language\AST\OperationDefinitionNode $operation) use($context) : void {
            $usages = $context->getRecursiveVariableUsages($operation);
            foreach ($usages as $usage) {
                $node = $usage['node'];
                $type = $usage['type'];
                $defaultValue = $usage['defaultValue'];
                $varName = $node->name->value;
                $varDef = $this->varDefMap[$varName] ?? null;
                if ($varDef === null || $type === null) {
                    continue;
                }
                // A var type is allowed if it is the same or more strict (e.g. is
                // a subtype of) than the expected type. It can be more strict if
                // the variable type is non-null when the expected type is nullable.
                // If both are list types, the variable item type can be more strict
                // than the expected item type (contravariant).
                $schema = $context->getSchema();
                $varType = \Builderius\GraphQL\Utils\TypeInfo::typeFromAST($schema, $varDef->type);
                if (!$varType || $this->allowedVariableUsage($schema, $varType, $varDef->defaultValue, $type, $defaultValue)) {
                    continue;
                }
                $context->reportError(new \Builderius\GraphQL\Error\Error(self::badVarPosMessage($varName, $varType, $type), [$varDef, $node]));
            }
        }], \Builderius\GraphQL\Language\AST\NodeKind::VARIABLE_DEFINITION => function (\Builderius\GraphQL\Language\AST\VariableDefinitionNode $varDefNode) : void {
            $this->varDefMap[$varDefNode->variable->name->value] = $varDefNode;
        }];
    }
    /**
     * A var type is allowed if it is the same or more strict than the expected
     * type. It can be more strict if the variable type is non-null when the
     * expected type is nullable. If both are list types, the variable item type can
     * be more strict than the expected item type.
     */
    public static function badVarPosMessage($varName, $varType, $expectedType)
    {
        return \sprintf('Variable "$%s" of type "%s" used in position expecting type "%s".', $varName, $varType, $expectedType);
    }
    /**
     * Returns true if the variable is allowed in the location it was found,
     * which includes considering if default values exist for either the variable
     * or the location at which it is located.
     *
     * @param ValueNode|null $varDefaultValue
     * @param mixed          $locationDefaultValue
     */
    private function allowedVariableUsage(\Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Type\Definition\Type $varType, $varDefaultValue, \Builderius\GraphQL\Type\Definition\Type $locationType, $locationDefaultValue) : bool
    {
        if ($locationType instanceof \Builderius\GraphQL\Type\Definition\NonNull && !$varType instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
            $hasNonNullVariableDefaultValue = $varDefaultValue && !$varDefaultValue instanceof \Builderius\GraphQL\Language\AST\NullValueNode;
            $hasLocationDefaultValue = !\Builderius\GraphQL\Utils\Utils::isInvalid($locationDefaultValue);
            if (!$hasNonNullVariableDefaultValue && !$hasLocationDefaultValue) {
                return \false;
            }
            $nullableLocationType = $locationType->getWrappedType();
            return \Builderius\GraphQL\Utils\TypeComparators::isTypeSubTypeOf($schema, $varType, $nullableLocationType);
        }
        return \Builderius\GraphQL\Utils\TypeComparators::isTypeSubTypeOf($schema, $varType, $locationType);
    }
}
