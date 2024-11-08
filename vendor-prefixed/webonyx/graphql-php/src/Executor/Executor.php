<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Executor;

use ArrayAccess;
use Closure;
use Builderius\GraphQL\Cache\GraphQLObjectCache;
use Builderius\GraphQL\Executor\Promise\Adapter\SyncPromiseAdapter;
use Builderius\GraphQL\Executor\Promise\Promise;
use Builderius\GraphQL\Executor\Promise\PromiseAdapter;
use Builderius\GraphQL\Language\AST\DocumentNode;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\GraphQL\Type\Schema;
use function is_array;
use function is_object;
/**
 * Implements the "Evaluating requests" section of the GraphQL specification.
 */
class Executor
{
    /** @var callable */
    private static $defaultFieldResolver = [self::class, 'defaultFieldResolver'];
    /** @var PromiseAdapter */
    private static $defaultPromiseAdapter;
    /** @var callable */
    private static $implementationFactory = [\Builderius\GraphQL\Executor\ReferenceExecutor::class, 'create'];
    public static function getDefaultFieldResolver() : callable
    {
        return self::$defaultFieldResolver;
    }
    /**
     * Set a custom default resolve function.
     */
    public static function setDefaultFieldResolver(callable $fieldResolver)
    {
        self::$defaultFieldResolver = $fieldResolver;
    }
    public static function getPromiseAdapter() : \Builderius\GraphQL\Executor\Promise\PromiseAdapter
    {
        return self::$defaultPromiseAdapter ?? (self::$defaultPromiseAdapter = new \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromiseAdapter());
    }
    /**
     * Set a custom default promise adapter.
     */
    public static function setPromiseAdapter(?\Builderius\GraphQL\Executor\Promise\PromiseAdapter $defaultPromiseAdapter = null)
    {
        self::$defaultPromiseAdapter = $defaultPromiseAdapter;
    }
    public static function getImplementationFactory() : callable
    {
        return self::$implementationFactory;
    }
    /**
     * Set a custom executor implementation factory.
     */
    public static function setImplementationFactory(callable $implementationFactory)
    {
        self::$implementationFactory = $implementationFactory;
    }
    /**
     * Executes DocumentNode against given $schema.
     *
     * Always returns ExecutionResult and never throws.
     * All errors which occur during operation execution are collected in `$result->errors`.
     *
     * @param mixed|null                    $rootValue
     * @param mixed|null                    $contextValue
     * @param array<mixed>|ArrayAccess|null $variableValues
     * @param string|null                   $operationName
     *
     * @return ExecutionResult|Promise
     *
     * @api
     */
    public static function execute(\Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Language\AST\DocumentNode $documentNode, $rootValue = null, $contextValue = null, $variableValues = null, $operationName = null, ?callable $fieldResolver = null)
    {
        // TODO: deprecate (just always use SyncAdapter here) and have `promiseToExecute()` for other cases
        $promiseAdapter = static::getPromiseAdapter();
        $result = static::promiseToExecute($promiseAdapter, $schema, $documentNode, $rootValue, $contextValue, $variableValues, $operationName, $fieldResolver);
        if ($promiseAdapter instanceof \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromiseAdapter) {
            $result = $promiseAdapter->wait($result);
        }
        return $result;
    }
    /**
     * Same as execute(), but requires promise adapter and returns a promise which is always
     * fulfilled with an instance of ExecutionResult and never rejected.
     *
     * Useful for async PHP platforms.
     *
     * @param mixed|null        $rootValue
     * @param mixed|null        $contextValue
     * @param array<mixed>|null $variableValues
     * @param string|null       $operationName
     *
     * @return Promise
     *
     * @api
     */
    public static function promiseToExecute(\Builderius\GraphQL\Executor\Promise\PromiseAdapter $promiseAdapter, \Builderius\GraphQL\Type\Schema $schema, \Builderius\GraphQL\Language\AST\DocumentNode $documentNode, $rootValue = null, $contextValue = null, $variableValues = null, $operationName = null, ?callable $fieldResolver = null, \Builderius\GraphQL\Cache\GraphQLObjectCache $cache = null, \Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher $eventDispatcher = null)
    {
        $factory = self::$implementationFactory;
        /** @var ExecutorImplementation $executor */
        $executor = $factory($promiseAdapter, $schema, $documentNode, $rootValue, $contextValue, $variableValues, $operationName, $fieldResolver ?? self::$defaultFieldResolver, $cache, $eventDispatcher);
        return $executor->doExecute();
    }
    /**
     * If a resolve function is not given, then a default resolve behavior is used
     * which takes the property of the root value of the same name as the field
     * and returns it as the result, or if it's a function, returns the result
     * of calling that function while passing along args and context.
     *
     * @param mixed                $objectValue
     * @param array<string, mixed> $args
     * @param mixed|null           $contextValue
     *
     * @return mixed|null
     */
    public static function defaultFieldResolver($objectValue, $args, $contextValue, \Builderius\GraphQL\Type\Definition\ResolveInfo $info)
    {
        $fieldName = $info->fieldName;
        $property = null;
        if (\is_array($objectValue) || $objectValue instanceof \ArrayAccess) {
            if (isset($objectValue[$fieldName])) {
                $property = $objectValue[$fieldName];
            }
        } elseif (\is_object($objectValue)) {
            if (isset($objectValue->{$fieldName})) {
                $property = $objectValue->{$fieldName};
            }
        }
        return $property instanceof \Closure ? $property($objectValue, $args, $contextValue, $info) : $property;
    }
}
