<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Experimental\Executor;

use Generator;
use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Error\InvariantViolation;
use Builderius\GraphQL\Error\Warning;
use Builderius\GraphQL\Executor\ExecutionResult;
use Builderius\GraphQL\Executor\ExecutorImplementation;
use Builderius\GraphQL\Executor\Promise\Promise;
use Builderius\GraphQL\Executor\Promise\PromiseAdapter;
use Builderius\GraphQL\Executor\Values;
use Builderius\GraphQL\Language\AST\DocumentNode;
use Builderius\GraphQL\Language\AST\SelectionSetNode;
use Builderius\GraphQL\Language\AST\ValueNode;
use Builderius\GraphQL\Type\Definition\AbstractType;
use Builderius\GraphQL\Type\Definition\CompositeType;
use Builderius\GraphQL\Type\Definition\EnumType;
use Builderius\GraphQL\Type\Definition\InputObjectType;
use Builderius\GraphQL\Type\Definition\InputType;
use Builderius\GraphQL\Type\Definition\InterfaceType;
use Builderius\GraphQL\Type\Definition\LeafType;
use Builderius\GraphQL\Type\Definition\ListOfType;
use Builderius\GraphQL\Type\Definition\NonNull;
use Builderius\GraphQL\Type\Definition\ObjectType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\GraphQL\Type\Definition\ScalarType;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Definition\UnionType;
use Builderius\GraphQL\Type\Introspection;
use Builderius\GraphQL\Type\Schema;
use Builderius\GraphQL\Utils\AST;
use Builderius\GraphQL\Utils\Utils;
use SplQueue;
use stdClass;
use Throwable;
use function count;
use function is_array;
use function is_string;
use function sprintf;
class CoroutineExecutor implements \Builderius\GraphQL\Experimental\Executor\Runtime, \Builderius\GraphQL\Executor\ExecutorImplementation
{
    /** @var object */
    private static $undefined;
    /** @var Schema */
    private $schema;
    /** @var callable */
    private $fieldResolver;
    /** @var PromiseAdapter */
    private $promiseAdapter;
    /** @var mixed|null */
    private $rootValue;
    /** @var mixed|null */
    private $contextValue;
    /** @var mixed|null */
    private $rawVariableValues;
    /** @var mixed|null */
    private $variableValues;
    /** @var DocumentNode */
    private $documentNode;
    /** @var string|null */
    private $operationName;
    /** @var Collector|null */
    private $collector;
    /** @var array<Error> */
    private $errors;
    /** @var SplQueue */
    private $queue;
    /** @var SplQueue */
    private $schedule;
    /** @var stdClass|null */
    private $rootResult;
    /** @var int|null */
    private $pending;
    /** @var callable */
    private $doResolve;
    public function __construct(\Builderius\GraphQL\Executor\Promise\PromiseAdapter $promiseAdapter, \Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Language\AST\DocumentNode $documentNode, $rootValue, $contextValue, $rawVariableValues, ?string $operationName, callable $fieldResolver)
    {
        if (self::$undefined === null) {
            self::$undefined = \Builderius\GraphQL\Utils\Utils::undefined();
        }
        $this->errors = [];
        $this->queue = new \SplQueue();
        $this->schedule = new \SplQueue();
        $this->schema = $schema;
        $this->fieldResolver = $fieldResolver;
        $this->promiseAdapter = $promiseAdapter;
        $this->rootValue = $rootValue;
        $this->contextValue = $contextValue;
        $this->rawVariableValues = $rawVariableValues;
        $this->documentNode = $documentNode;
        $this->operationName = $operationName;
    }
    public static function create(\Builderius\GraphQL\Executor\Promise\PromiseAdapter $promiseAdapter, \Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Language\AST\DocumentNode $documentNode, $rootValue, $contextValue, $variableValues, ?string $operationName, callable $fieldResolver)
    {
        return new static($promiseAdapter, $schema, $documentNode, $rootValue, $contextValue, $variableValues, $operationName, $fieldResolver);
    }
    private static function resultToArray($value, $emptyObjectAsStdClass = \true)
    {
        if ($value instanceof \stdClass) {
            $array = (array) $value;
            foreach ($array as $propertyName => $propertyValue) {
                $array[$propertyName] = self::resultToArray($propertyValue);
            }
            if ($emptyObjectAsStdClass && \count($array) === 0) {
                return new \stdClass();
            }
            return $array;
        }
        if (\is_array($value)) {
            $array = [];
            foreach ($value as $key => $item) {
                $array[$key] = self::resultToArray($item);
            }
            return $array;
        }
        return $value;
    }
    public function doExecute() : \Builderius\GraphQL\Executor\Promise\Promise
    {
        $this->rootResult = new \stdClass();
        $this->errors = [];
        $this->queue = new \SplQueue();
        $this->schedule = new \SplQueue();
        $this->pending = 0;
        $this->collector = new \Builderius\GraphQL\Experimental\Executor\Collector($this->schema, $this);
        $this->collector->initialize($this->documentNode, $this->operationName);
        if (\count($this->errors) > 0) {
            return $this->promiseAdapter->createFulfilled($this->finishExecute(null, $this->errors));
        }
        [$errors, $coercedVariableValues] = \Builderius\GraphQL\Executor\Values::getVariableValues($this->schema, $this->collector->operation->variableDefinitions ?? [], $this->rawVariableValues ?? []);
        if (\count($errors ?? []) > 0) {
            return $this->promiseAdapter->createFulfilled($this->finishExecute(null, $errors));
        }
        $this->variableValues = $coercedVariableValues;
        foreach ($this->collector->collectFields($this->collector->rootType, $this->collector->operation->selectionSet) as $shared) {
            /** @var CoroutineContextShared $shared */
            // !!! assign to keep object keys sorted
            $this->rootResult->{$shared->resultName} = null;
            $ctx = new \Builderius\GraphQL\Experimental\Executor\CoroutineContext($shared, $this->collector->rootType, $this->rootValue, $this->rootResult, [$shared->resultName]);
            $fieldDefinition = $this->findFieldDefinition($ctx);
            if (!$fieldDefinition->getType() instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
                $ctx->nullFence = [$shared->resultName];
            }
            if ($this->collector->operation->operation === 'mutation' && !$this->queue->isEmpty()) {
                $this->schedule->enqueue($ctx);
            } else {
                $this->queue->enqueue(new \Builderius\GraphQL\Experimental\Executor\Strand($this->spawn($ctx)));
            }
        }
        $this->run();
        if ($this->pending > 0) {
            return $this->promiseAdapter->create(function (callable $resolve) : void {
                $this->doResolve = $resolve;
            });
        }
        return $this->promiseAdapter->createFulfilled($this->finishExecute($this->rootResult, $this->errors));
    }
    /**
     * @param object|null $value
     * @param Error[]     $errors
     */
    private function finishExecute($value, array $errors) : \Builderius\GraphQL\Executor\ExecutionResult
    {
        $this->rootResult = null;
        $this->errors = [];
        $this->queue = new \SplQueue();
        $this->schedule = new \SplQueue();
        $this->pending = null;
        $this->collector = null;
        $this->variableValues = null;
        if ($value !== null) {
            $value = self::resultToArray($value, \false);
        }
        return new \Builderius\GraphQL\Executor\ExecutionResult($value, $errors);
    }
    /**
     * @internal
     *
     * @param ScalarType|EnumType|InputObjectType|ListOfType|NonNull $type
     */
    public function evaluate(\Builderius\GraphQL\Language\AST\ValueNode $valueNode, \Builderius\GraphQL\Type\Definition\InputType $type)
    {
        return \Builderius\GraphQL\Utils\AST::valueFromAST($valueNode, $type, $this->variableValues);
    }
    /**
     * @internal
     */
    public function addError($error)
    {
        $this->errors[] = $error;
    }
    private function run()
    {
        RUN:
        while (!$this->queue->isEmpty()) {
            /** @var Strand $strand */
            $strand = $this->queue->dequeue();
            try {
                if ($strand->success !== null) {
                    RESUME:
                    if ($strand->success) {
                        $strand->current->send($strand->value);
                    } else {
                        $strand->current->throw($strand->value);
                    }
                    $strand->success = null;
                    $strand->value = null;
                }
                START:
                if ($strand->current->valid()) {
                    $value = $strand->current->current();
                    if ($value instanceof \Generator) {
                        $strand->stack[$strand->depth++] = $strand->current;
                        $strand->current = $value;
                        goto START;
                    } elseif ($this->isPromise($value)) {
                        // !!! increment pending before calling ->then() as it may invoke the callback right away
                        ++$this->pending;
                        if (!$value instanceof \Builderius\GraphQL\Executor\Promise\Promise) {
                            $value = $this->promiseAdapter->convertThenable($value);
                        }
                        $this->promiseAdapter->then($value, function ($value) use($strand) : void {
                            $strand->success = \true;
                            $strand->value = $value;
                            $this->queue->enqueue($strand);
                            $this->done();
                        }, function (\Throwable $throwable) use($strand) : void {
                            $strand->success = \false;
                            $strand->value = $throwable;
                            $this->queue->enqueue($strand);
                            $this->done();
                        });
                        continue;
                    } else {
                        $strand->success = \true;
                        $strand->value = $value;
                        goto RESUME;
                    }
                }
                $strand->success = \true;
                $strand->value = $strand->current->getReturn();
            } catch (\Throwable $reason) {
                $strand->success = \false;
                $strand->value = $reason;
            }
            if ($strand->depth <= 0) {
                continue;
            }
            $current =& $strand->stack[--$strand->depth];
            $strand->current = $current;
            $current = null;
            goto RESUME;
        }
        if ($this->pending > 0 || $this->schedule->isEmpty()) {
            return;
        }
        /** @var CoroutineContext $ctx */
        $ctx = $this->schedule->dequeue();
        $this->queue->enqueue(new \Builderius\GraphQL\Experimental\Executor\Strand($this->spawn($ctx)));
        goto RUN;
    }
    private function done()
    {
        --$this->pending;
        $this->run();
        if ($this->pending > 0) {
            return;
        }
        $doResolve = $this->doResolve;
        $doResolve($this->finishExecute($this->rootResult, $this->errors));
    }
    private function spawn(\Builderius\GraphQL\Experimental\Executor\CoroutineContext $ctx)
    {
        // short-circuit evaluation for __typename
        if ($ctx->shared->fieldName === \Builderius\GraphQL\Type\Introspection::TYPE_NAME_FIELD_NAME) {
            $ctx->result->{$ctx->shared->resultName} = $ctx->type->name;
            return;
        }
        try {
            if ($ctx->shared->typeGuard1 === $ctx->type) {
                $resolve = $ctx->shared->resolveIfType1;
                $ctx->resolveInfo = clone $ctx->shared->resolveInfoIfType1;
                $ctx->resolveInfo->path = $ctx->path;
                $arguments = $ctx->shared->argumentsIfType1;
                $returnType = $ctx->resolveInfo->returnType;
            } else {
                $fieldDefinition = $this->findFieldDefinition($ctx);
                if ($fieldDefinition->resolveFn !== null) {
                    $resolve = $fieldDefinition->resolveFn;
                } elseif ($ctx->type->resolveFieldFn !== null) {
                    $resolve = $ctx->type->resolveFieldFn;
                } else {
                    $resolve = $this->fieldResolver;
                }
                $returnType = $fieldDefinition->getType();
                $ctx->resolveInfo = new \Builderius\GraphQL\Type\Definition\ResolveInfo($fieldDefinition, $ctx->shared->fieldNodes, $ctx->type, $ctx->path, $this->schema, $this->collector->fragments, $this->rootValue, $this->collector->operation, $this->variableValues);
                $arguments = \Builderius\GraphQL\Executor\Values::getArgumentValuesForMap($fieldDefinition, $ctx->shared->argumentValueMap, $this->variableValues);
                // !!! assign only in batch when no exception can be thrown in-between
                $ctx->shared->typeGuard1 = $ctx->type;
                $ctx->shared->resolveIfType1 = $resolve;
                $ctx->shared->argumentsIfType1 = $arguments;
                $ctx->shared->resolveInfoIfType1 = $ctx->resolveInfo;
            }
            $value = $resolve($ctx->value, $arguments, $this->contextValue, $ctx->resolveInfo);
            if (!$this->completeValueFast($ctx, $returnType, $value, $ctx->path, $returnValue)) {
                $returnValue = (yield $this->completeValue($ctx, $returnType, $value, $ctx->path, $ctx->nullFence));
            }
        } catch (\Throwable $reason) {
            $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError($reason, $ctx->shared->fieldNodes, $ctx->path));
            $returnValue = self::$undefined;
        }
        if ($returnValue !== self::$undefined) {
            $ctx->result->{$ctx->shared->resultName} = $returnValue;
        } elseif ($ctx->resolveInfo !== null && $ctx->resolveInfo->returnType instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
            // !!! $ctx->resolveInfo might not have been initialized yet
            $result =& $this->rootResult;
            foreach ($ctx->nullFence ?? [] as $key) {
                if (\is_string($key)) {
                    $result =& $result->{$key};
                } else {
                    $result =& $result[$key];
                }
            }
            $result = null;
        }
    }
    private function findFieldDefinition(\Builderius\GraphQL\Experimental\Executor\CoroutineContext $ctx)
    {
        if ($ctx->shared->fieldName === \Builderius\GraphQL\Type\Introspection::SCHEMA_FIELD_NAME && $ctx->type === $this->schema->getQueryType()) {
            return \Builderius\GraphQL\Type\Introspection::schemaMetaFieldDef();
        }
        if ($ctx->shared->fieldName === \Builderius\GraphQL\Type\Introspection::TYPE_FIELD_NAME && $ctx->type === $this->schema->getQueryType()) {
            return \Builderius\GraphQL\Type\Introspection::typeMetaFieldDef();
        }
        if ($ctx->shared->fieldName === \Builderius\GraphQL\Type\Introspection::TYPE_NAME_FIELD_NAME) {
            return \Builderius\GraphQL\Type\Introspection::typeNameMetaFieldDef();
        }
        return $ctx->type->getField($ctx->shared->fieldName);
    }
    /**
     * @param mixed    $value
     * @param string[] $path
     * @param mixed    $returnValue
     */
    private function completeValueFast(\Builderius\GraphQL\Experimental\Executor\CoroutineContext $ctx, \Builderius\GraphQL\Type\Definition\Type $type, $value, array $path, &$returnValue) : bool
    {
        // special handling of Throwable inherited from JS reference implementation, but makes no sense in this PHP
        if ($this->isPromise($value) || $value instanceof \Throwable) {
            return \false;
        }
        $nonNull = \false;
        if ($type instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
            $nonNull = \true;
            $type = $type->getWrappedType();
        }
        if (!$type instanceof \Builderius\GraphQL\Type\Definition\LeafType) {
            return \false;
        }
        if ($type !== $this->schema->getType($type->name)) {
            $hint = '';
            if ($this->schema->getConfig()->typeLoader !== null) {
                $hint = \sprintf('Make sure that type loader returns the same instance as defined in %s.%s', $ctx->type, $ctx->shared->fieldName);
            }
            $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError(new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('Schema must contain unique named types but contains multiple types named "%s". %s ' . '(see http://webonyx.github.io/graphql-php/type-system/#type-registry).', $type->name, $hint)), $ctx->shared->fieldNodes, $path));
            $value = null;
        }
        if ($value === null) {
            $returnValue = null;
        } else {
            try {
                $returnValue = $type->serialize($value);
            } catch (\Throwable $error) {
                $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError(new \Builderius\GraphQL\Error\InvariantViolation('Expected a value of type "' . \Builderius\GraphQL\Utils\Utils::printSafe($type) . '" but received: ' . \Builderius\GraphQL\Utils\Utils::printSafe($value), 0, $error), $ctx->shared->fieldNodes, $path));
                $returnValue = null;
            }
        }
        if ($nonNull && $returnValue === null) {
            $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError(new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('Cannot return null for non-nullable field "%s.%s".', $ctx->type->name, $ctx->shared->fieldName)), $ctx->shared->fieldNodes, $path));
            $returnValue = self::$undefined;
        }
        return \true;
    }
    /**
     * @param mixed         $value
     * @param string[]      $path
     * @param string[]|null $nullFence
     *
     * @return mixed
     */
    private function completeValue(\Builderius\GraphQL\Experimental\Executor\CoroutineContext $ctx, \Builderius\GraphQL\Type\Definition\Type $type, $value, array $path, ?array $nullFence)
    {
        $nonNull = \false;
        $returnValue = null;
        if ($type instanceof \Builderius\GraphQL\Type\Definition\NonNull) {
            $nonNull = \true;
            $type = $type->getWrappedType();
        } else {
            $nullFence = $path;
        }
        // !!! $value might be promise, yield to resolve
        try {
            if ($this->isPromise($value)) {
                $value = (yield $value);
            }
        } catch (\Throwable $reason) {
            $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError($reason, $ctx->shared->fieldNodes, $path));
            if ($nonNull) {
                $returnValue = self::$undefined;
            } else {
                $returnValue = null;
            }
            goto CHECKED_RETURN;
        }
        if ($value === null) {
            $returnValue = $value;
            goto CHECKED_RETURN;
        } elseif ($value instanceof \Throwable) {
            // special handling of Throwable inherited from JS reference implementation, but makes no sense in this PHP
            $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError($value, $ctx->shared->fieldNodes, $path));
            if ($nonNull) {
                $returnValue = self::$undefined;
            } else {
                $returnValue = null;
            }
            goto CHECKED_RETURN;
        }
        if ($type instanceof \Builderius\GraphQL\Type\Definition\ListOfType) {
            $returnValue = [];
            $index = -1;
            $itemType = $type->getWrappedType();
            foreach ($value as $itemValue) {
                ++$index;
                $itemPath = $path;
                $itemPath[] = $index;
                // !!! use arrays COW semantics
                $ctx->resolveInfo->path = $itemPath;
                try {
                    if (!$this->completeValueFast($ctx, $itemType, $itemValue, $itemPath, $itemReturnValue)) {
                        $itemReturnValue = (yield $this->completeValue($ctx, $itemType, $itemValue, $itemPath, $nullFence));
                    }
                } catch (\Throwable $reason) {
                    $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError($reason, $ctx->shared->fieldNodes, $itemPath));
                    $itemReturnValue = null;
                }
                if ($itemReturnValue === self::$undefined) {
                    $returnValue = self::$undefined;
                    goto CHECKED_RETURN;
                }
                $returnValue[$index] = $itemReturnValue;
            }
            goto CHECKED_RETURN;
        } else {
            if ($type !== $this->schema->getType($type->name)) {
                $hint = '';
                if ($this->schema->getConfig()->typeLoader !== null) {
                    $hint = \sprintf('Make sure that type loader returns the same instance as defined in %s.%s', $ctx->type, $ctx->shared->fieldName);
                }
                $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError(new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('Schema must contain unique named types but contains multiple types named "%s". %s ' . '(see http://webonyx.github.io/graphql-php/type-system/#type-registry).', $type->name, $hint)), $ctx->shared->fieldNodes, $path));
                $returnValue = null;
                goto CHECKED_RETURN;
            }
            if ($type instanceof \Builderius\GraphQL\Type\Definition\LeafType) {
                try {
                    $returnValue = $type->serialize($value);
                } catch (\Throwable $error) {
                    $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError(new \Builderius\GraphQL\Error\InvariantViolation('Expected a value of type "' . \Builderius\GraphQL\Utils\Utils::printSafe($type) . '" but received: ' . \Builderius\GraphQL\Utils\Utils::printSafe($value), 0, $error), $ctx->shared->fieldNodes, $path));
                    $returnValue = null;
                }
                goto CHECKED_RETURN;
            } elseif ($type instanceof \Builderius\GraphQL\Type\Definition\CompositeType) {
                /** @var ObjectType|null $objectType */
                $objectType = null;
                if ($type instanceof \Builderius\GraphQL\Type\Definition\InterfaceType || $type instanceof \Builderius\GraphQL\Type\Definition\UnionType) {
                    $objectType = $type->resolveType($value, $this->contextValue, $ctx->resolveInfo);
                    if ($objectType === null) {
                        $objectType = (yield $this->resolveTypeSlow($ctx, $value, $type));
                    }
                    // !!! $objectType->resolveType() might return promise, yield to resolve
                    $objectType = (yield $objectType);
                    if (\is_string($objectType)) {
                        $objectType = $this->schema->getType($objectType);
                    }
                    if ($objectType === null) {
                        $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError(\sprintf('Composite type "%s" did not resolve concrete object type for value: %s.', $type->name, \Builderius\GraphQL\Utils\Utils::printSafe($value)), $ctx->shared->fieldNodes, $path));
                        $returnValue = self::$undefined;
                        goto CHECKED_RETURN;
                    } elseif (!$objectType instanceof \Builderius\GraphQL\Type\Definition\ObjectType) {
                        $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError(new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('Abstract type %s must resolve to an Object type at ' . 'runtime for field %s.%s with value "%s", received "%s". ' . 'Either the %s type should provide a "resolveType" ' . 'function or each possible type should provide an "isTypeOf" function.', $type, $ctx->resolveInfo->parentType, $ctx->resolveInfo->fieldName, \Builderius\GraphQL\Utils\Utils::printSafe($value), \Builderius\GraphQL\Utils\Utils::printSafe($objectType), $type)), $ctx->shared->fieldNodes, $path));
                        $returnValue = null;
                        goto CHECKED_RETURN;
                    } elseif (!$this->schema->isPossibleType($type, $objectType)) {
                        $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError(new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('Runtime Object type "%s" is not a possible type for "%s".', $objectType, $type)), $ctx->shared->fieldNodes, $path));
                        $returnValue = null;
                        goto CHECKED_RETURN;
                    } elseif ($objectType !== $this->schema->getType($objectType->name)) {
                        $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError(new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('Schema must contain unique named types but contains multiple types named "%s". ' . 'Make sure that `resolveType` function of abstract type "%s" returns the same ' . 'type instance as referenced anywhere else within the schema ' . '(see http://webonyx.github.io/graphql-php/type-system/#type-registry).', $objectType, $type)), $ctx->shared->fieldNodes, $path));
                        $returnValue = null;
                        goto CHECKED_RETURN;
                    }
                } elseif ($type instanceof \Builderius\GraphQL\Type\Definition\ObjectType) {
                    $objectType = $type;
                } else {
                    $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError(\sprintf('Unexpected field type "%s".', \Builderius\GraphQL\Utils\Utils::printSafe($type)), $ctx->shared->fieldNodes, $path));
                    $returnValue = self::$undefined;
                    goto CHECKED_RETURN;
                }
                $typeCheck = $objectType->isTypeOf($value, $this->contextValue, $ctx->resolveInfo);
                if ($typeCheck !== null) {
                    // !!! $objectType->isTypeOf() might return promise, yield to resolve
                    $typeCheck = (yield $typeCheck);
                    if (!$typeCheck) {
                        $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError(\sprintf('Expected value of type "%s" but got: %s.', $type->name, \Builderius\GraphQL\Utils\Utils::printSafe($value)), $ctx->shared->fieldNodes, $path));
                        $returnValue = null;
                        goto CHECKED_RETURN;
                    }
                }
                $returnValue = new \stdClass();
                if ($ctx->shared->typeGuard2 === $objectType) {
                    foreach ($ctx->shared->childContextsIfType2 as $childCtx) {
                        $childCtx = clone $childCtx;
                        $childCtx->type = $objectType;
                        $childCtx->value = $value;
                        $childCtx->result = $returnValue;
                        $childCtx->path = $path;
                        $childCtx->path[] = $childCtx->shared->resultName;
                        // !!! uses array COW semantics
                        $childCtx->nullFence = $nullFence;
                        $childCtx->resolveInfo = null;
                        $this->queue->enqueue(new \Builderius\GraphQL\Experimental\Executor\Strand($this->spawn($childCtx)));
                        // !!! assign null to keep object keys sorted
                        $returnValue->{$childCtx->shared->resultName} = null;
                    }
                } else {
                    $childContexts = [];
                    $fields = [];
                    if ($this->collector !== null) {
                        $fields = $this->collector->collectFields($objectType, $ctx->shared->mergedSelectionSet ?? $this->mergeSelectionSets($ctx));
                    }
                    /** @var CoroutineContextShared $childShared */
                    foreach ($fields as $childShared) {
                        $childPath = $path;
                        $childPath[] = $childShared->resultName;
                        // !!! uses array COW semantics
                        $childCtx = new \Builderius\GraphQL\Experimental\Executor\CoroutineContext($childShared, $objectType, $value, $returnValue, $childPath, $nullFence);
                        $childContexts[] = $childCtx;
                        $this->queue->enqueue(new \Builderius\GraphQL\Experimental\Executor\Strand($this->spawn($childCtx)));
                        // !!! assign null to keep object keys sorted
                        $returnValue->{$childShared->resultName} = null;
                    }
                    $ctx->shared->typeGuard2 = $objectType;
                    $ctx->shared->childContextsIfType2 = $childContexts;
                }
                goto CHECKED_RETURN;
            } else {
                $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError(\sprintf('Unhandled type "%s".', \Builderius\GraphQL\Utils\Utils::printSafe($type)), $ctx->shared->fieldNodes, $path));
                $returnValue = null;
                goto CHECKED_RETURN;
            }
        }
        CHECKED_RETURN:
        if ($nonNull && $returnValue === null) {
            $this->addError(\Builderius\GraphQL\Error\Error::createLocatedError(new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('Cannot return null for non-nullable field "%s.%s".', $ctx->type->name, $ctx->shared->fieldName)), $ctx->shared->fieldNodes, $path));
            return self::$undefined;
        }
        return $returnValue;
    }
    private function mergeSelectionSets(\Builderius\GraphQL\Experimental\Executor\CoroutineContext $ctx)
    {
        $selections = [];
        foreach ($ctx->shared->fieldNodes as $fieldNode) {
            if ($fieldNode->selectionSet === null) {
                continue;
            }
            foreach ($fieldNode->selectionSet->selections as $selection) {
                $selections[] = $selection;
            }
        }
        return $ctx->shared->mergedSelectionSet = new \Builderius\GraphQL\Language\AST\SelectionSetNode(['selections' => $selections]);
    }
    /**
     * @param InterfaceType|UnionType $abstractType
     *
     * @return Generator|ObjectType|Type|null
     */
    private function resolveTypeSlow(\Builderius\GraphQL\Experimental\Executor\CoroutineContext $ctx, $value, \Builderius\GraphQL\Type\Definition\AbstractType $abstractType)
    {
        if ($value !== null && \is_array($value) && isset($value['__typename']) && \is_string($value['__typename'])) {
            return $this->schema->getType($value['__typename']);
        }
        if ($abstractType instanceof \Builderius\GraphQL\Type\Definition\InterfaceType && $this->schema->getConfig()->typeLoader !== null) {
            \Builderius\GraphQL\Error\Warning::warnOnce(\sprintf('GraphQL Interface Type `%s` returned `null` from its `resolveType` function ' . 'for value: %s. Switching to slow resolution method using `isTypeOf` ' . 'of all possible implementations. It requires full schema scan and degrades query performance significantly. ' . ' Make sure your `resolveType` always returns valid implementation or throws.', $abstractType->name, \Builderius\GraphQL\Utils\Utils::printSafe($value)), \Builderius\GraphQL\Error\Warning::WARNING_FULL_SCHEMA_SCAN);
        }
        $possibleTypes = $this->schema->getPossibleTypes($abstractType);
        // to be backward-compatible with old executor, ->isTypeOf() is called for all possible types,
        // it cannot short-circuit when the match is found
        $selectedType = null;
        foreach ($possibleTypes as $type) {
            $typeCheck = (yield $type->isTypeOf($value, $this->contextValue, $ctx->resolveInfo));
            if ($selectedType !== null || !$typeCheck) {
                continue;
            }
            $selectedType = $type;
        }
        return $selectedType;
    }
    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function isPromise($value)
    {
        return $value instanceof \Builderius\GraphQL\Executor\Promise\Promise || $this->promiseAdapter->isThenable($value);
    }
}
