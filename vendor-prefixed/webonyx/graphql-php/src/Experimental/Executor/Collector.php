<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Experimental\Executor;

use Generator;
use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\BooleanValueNode;
use Builderius\GraphQL\Language\AST\DefinitionNode;
use Builderius\GraphQL\Language\AST\DocumentNode;
use Builderius\GraphQL\Language\AST\EnumValueNode;
use Builderius\GraphQL\Language\AST\FieldNode;
use Builderius\GraphQL\Language\AST\FloatValueNode;
use Builderius\GraphQL\Language\AST\FragmentDefinitionNode;
use Builderius\GraphQL\Language\AST\FragmentSpreadNode;
use Builderius\GraphQL\Language\AST\InlineFragmentNode;
use Builderius\GraphQL\Language\AST\IntValueNode;
use Builderius\GraphQL\Language\AST\ListValueNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\NullValueNode;
use Builderius\GraphQL\Language\AST\ObjectValueNode;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Language\AST\SelectionSetNode;
use Builderius\GraphQL\Language\AST\StringValueNode;
use Builderius\GraphQL\Language\AST\VariableNode;
use Builderius\GraphQL\Type\Definition\AbstractType;
use Builderius\GraphQL\Type\Definition\Directive;
use Builderius\GraphQL\Type\Definition\ObjectType;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Introspection;
use Builderius\GraphQL\Type\Schema;
use function count;
use function sprintf;
/**
 * @internal
 */
class Collector
{
    /** @var Schema */
    private $schema;
    /** @var Runtime */
    private $runtime;
    /** @var OperationDefinitionNode|null */
    public $operation = null;
    /** @var FragmentDefinitionNode[] */
    public $fragments = [];
    /** @var ObjectType|null */
    public $rootType;
    /** @var FieldNode[][] */
    private $fields;
    /** @var array<string, bool> */
    private $visitedFragments;
    public function __construct(\Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Experimental\Executor\Runtime $runtime)
    {
        $this->schema = $schema;
        $this->runtime = $runtime;
    }
    public function initialize(\Builderius\GraphQL\Language\AST\DocumentNode $documentNode, ?string $operationName = null)
    {
        $hasMultipleAssumedOperations = \false;
        foreach ($documentNode->definitions as $definitionNode) {
            /** @var DefinitionNode|Node $definitionNode */
            if ($definitionNode instanceof \Builderius\GraphQL\Language\AST\OperationDefinitionNode) {
                if ($operationName === null && $this->operation !== null) {
                    $hasMultipleAssumedOperations = \true;
                }
                if ($operationName === null || isset($definitionNode->name) && $definitionNode->name->value === $operationName) {
                    $this->operation = $definitionNode;
                }
            } elseif ($definitionNode instanceof \Builderius\GraphQL\Language\AST\FragmentDefinitionNode) {
                $this->fragments[$definitionNode->name->value] = $definitionNode;
            }
        }
        if ($this->operation === null) {
            if ($operationName !== null) {
                $this->runtime->addError(new \Builderius\GraphQL\Error\Error(\sprintf('Unknown operation named "%s".', $operationName)));
            } else {
                $this->runtime->addError(new \Builderius\GraphQL\Error\Error('Must provide an operation.'));
            }
            return;
        }
        if ($hasMultipleAssumedOperations) {
            $this->runtime->addError(new \Builderius\GraphQL\Error\Error('Must provide operation name if query contains multiple operations.'));
            return;
        }
        if ($this->operation->operation === 'query') {
            $this->rootType = $this->schema->getQueryType();
        } elseif ($this->operation->operation === 'mutation') {
            $this->rootType = $this->schema->getMutationType();
        } elseif ($this->operation->operation === 'subscription') {
            $this->rootType = $this->schema->getSubscriptionType();
        } else {
            $this->runtime->addError(new \Builderius\GraphQL\Error\Error(\sprintf('Cannot initialize collector with operation type "%s".', $this->operation->operation)));
        }
    }
    /**
     * @return Generator
     */
    public function collectFields(\Builderius\GraphQL\Type\Definition\ObjectType $runtimeType, ?\Builderius\GraphQL\Language\AST\SelectionSetNode $selectionSet)
    {
        $this->fields = [];
        $this->visitedFragments = [];
        $this->doCollectFields($runtimeType, $selectionSet);
        foreach ($this->fields as $resultName => $fieldNodes) {
            $fieldNode = $fieldNodes[0];
            $fieldName = $fieldNode->name->value;
            $argumentValueMap = null;
            if (\count($fieldNode->arguments) > 0) {
                foreach ($fieldNode->arguments as $argumentNode) {
                    $argumentValueMap = $argumentValueMap ?? [];
                    $argumentValueMap[$argumentNode->name->value] = $argumentNode->value;
                }
            }
            if ($fieldName !== \Builderius\GraphQL\Type\Introspection::TYPE_NAME_FIELD_NAME && !($runtimeType === $this->schema->getQueryType() && ($fieldName === \Builderius\GraphQL\Type\Introspection::SCHEMA_FIELD_NAME || $fieldName === \Builderius\GraphQL\Type\Introspection::TYPE_FIELD_NAME)) && !$runtimeType->hasField($fieldName)) {
                // do not emit error
                continue;
            }
            (yield new \Builderius\GraphQL\Experimental\Executor\CoroutineContextShared($fieldNodes, $fieldName, $resultName, $argumentValueMap));
        }
    }
    private function doCollectFields(\Builderius\GraphQL\Type\Definition\ObjectType $runtimeType, ?\Builderius\GraphQL\Language\AST\SelectionSetNode $selectionSet)
    {
        if ($selectionSet === null) {
            return;
        }
        foreach ($selectionSet->selections as $selection) {
            /** @var FieldNode|FragmentSpreadNode|InlineFragmentNode $selection */
            if (\count($selection->directives) > 0) {
                foreach ($selection->directives as $directiveNode) {
                    if ($directiveNode->name->value === \Builderius\GraphQL\Type\Definition\Directive::SKIP_NAME) {
                        /** @var VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode|null $condition */
                        $condition = null;
                        foreach ($directiveNode->arguments as $argumentNode) {
                            if ($argumentNode->name->value === \Builderius\GraphQL\Type\Definition\Directive::IF_ARGUMENT_NAME) {
                                $condition = $argumentNode->value;
                                break;
                            }
                        }
                        if ($condition === null) {
                            $this->runtime->addError(new \Builderius\GraphQL\Error\Error(\sprintf('@%s directive is missing "%s" argument.', \Builderius\GraphQL\Type\Definition\Directive::SKIP_NAME, \Builderius\GraphQL\Type\Definition\Directive::IF_ARGUMENT_NAME), $selection));
                        } else {
                            if ($this->runtime->evaluate($condition, \Builderius\GraphQL\Type\Definition\Type::boolean()) === \true) {
                                continue 2;
                                // !!! advances outer loop
                            }
                        }
                    } elseif ($directiveNode->name->value === \Builderius\GraphQL\Type\Definition\Directive::INCLUDE_NAME) {
                        /** @var VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode|null $condition */
                        $condition = null;
                        foreach ($directiveNode->arguments as $argumentNode) {
                            if ($argumentNode->name->value === \Builderius\GraphQL\Type\Definition\Directive::IF_ARGUMENT_NAME) {
                                $condition = $argumentNode->value;
                                break;
                            }
                        }
                        if ($condition === null) {
                            $this->runtime->addError(new \Builderius\GraphQL\Error\Error(\sprintf('@%s directive is missing "%s" argument.', \Builderius\GraphQL\Type\Definition\Directive::INCLUDE_NAME, \Builderius\GraphQL\Type\Definition\Directive::IF_ARGUMENT_NAME), $selection));
                        } else {
                            if ($this->runtime->evaluate($condition, \Builderius\GraphQL\Type\Definition\Type::boolean()) !== \true) {
                                continue 2;
                                // !!! advances outer loop
                            }
                        }
                    }
                }
            }
            if ($selection instanceof \Builderius\GraphQL\Language\AST\FieldNode) {
                $resultName = $selection->alias === null ? $selection->name->value : $selection->alias->value;
                if (!isset($this->fields[$resultName])) {
                    $this->fields[$resultName] = [];
                }
                $this->fields[$resultName][] = $selection;
            } elseif ($selection instanceof \Builderius\GraphQL\Language\AST\FragmentSpreadNode) {
                $fragmentName = $selection->name->value;
                if (isset($this->visitedFragments[$fragmentName])) {
                    continue;
                }
                if (!isset($this->fragments[$fragmentName])) {
                    $this->runtime->addError(new \Builderius\GraphQL\Error\Error(\sprintf('Fragment "%s" does not exist.', $fragmentName), $selection));
                    continue;
                }
                $this->visitedFragments[$fragmentName] = \true;
                $fragmentDefinition = $this->fragments[$fragmentName];
                $conditionTypeName = $fragmentDefinition->typeCondition->name->value;
                if (!$this->schema->hasType($conditionTypeName)) {
                    $this->runtime->addError(new \Builderius\GraphQL\Error\Error(\sprintf('Cannot spread fragment "%s", type "%s" does not exist.', $fragmentName, $conditionTypeName), $selection));
                    continue;
                }
                $conditionType = $this->schema->getType($conditionTypeName);
                if ($conditionType instanceof \Builderius\GraphQL\Type\Definition\ObjectType) {
                    if ($runtimeType->name !== $conditionType->name) {
                        continue;
                    }
                } elseif ($conditionType instanceof \Builderius\GraphQL\Type\Definition\AbstractType) {
                    if (!$this->schema->isPossibleType($conditionType, $runtimeType)) {
                        continue;
                    }
                }
                $this->doCollectFields($runtimeType, $fragmentDefinition->selectionSet);
            } elseif ($selection instanceof \Builderius\GraphQL\Language\AST\InlineFragmentNode) {
                if ($selection->typeCondition !== null) {
                    $conditionTypeName = $selection->typeCondition->name->value;
                    if (!$this->schema->hasType($conditionTypeName)) {
                        $this->runtime->addError(new \Builderius\GraphQL\Error\Error(\sprintf('Cannot spread inline fragment, type "%s" does not exist.', $conditionTypeName), $selection));
                        continue;
                    }
                    $conditionType = $this->schema->getType($conditionTypeName);
                    if ($conditionType instanceof \Builderius\GraphQL\Type\Definition\ObjectType) {
                        if ($runtimeType->name !== $conditionType->name) {
                            continue;
                        }
                    } elseif ($conditionType instanceof \Builderius\GraphQL\Type\Definition\AbstractType) {
                        if (!$this->schema->isPossibleType($conditionType, $runtimeType)) {
                            continue;
                        }
                    }
                }
                $this->doCollectFields($runtimeType, $selection->selectionSet);
            }
        }
    }
}
