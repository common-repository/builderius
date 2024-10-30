<?php

namespace Builderius\Bundle\GraphQLBundle\Executor;

use Builderius\Bundle\GraphQLBundle\Event\GraphQLQueryBeforeExecutionEvent;
use Builderius\Bundle\GraphQLBundle\Event\GraphQLQueryExecutedEvent;
use Builderius\Bundle\GraphQLBundle\Provider\Directive\GraphQLDirectivesProviderInterface;
use Builderius\Bundle\GraphQLBundle\Provider\Type\GraphQLTypesProviderInterface;
use Builderius\Bundle\GraphQLBundle\Resolver\DefaultFieldResolver;
use Builderius\GraphQL\Cache\GraphQLObjectCache;
use Builderius\GraphQL\GraphQL;
use Builderius\GraphQL\Type\Schema;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;

class BuilderiusEntitiesGraphQLQueriesExecutor implements BuilderiusEntitiesGraphQLQueriesExecutorInterface
{
    /**
     * @var GraphQLTypesProviderInterface
     */
    private $graphqlTypesProvider;

    /**
     * @var GraphQLDirectivesProviderInterface
     */
    private $graphqlDirectivesProvider;

    /**
     * @var GraphQLObjectCache
     */
    private $graphqlSubfieldsCache;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param GraphQLTypesProviderInterface $graphqlTypesProvider
     * @param GraphQLDirectivesProviderInterface $graphqlDirectivesProvider
     * @param GraphQLObjectCache $graphqlSubfieldsCache
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        GraphQLTypesProviderInterface $graphqlTypesProvider,
        GraphQLDirectivesProviderInterface $graphqlDirectivesProvider,
        GraphQLObjectCache $graphqlSubfieldsCache,
        EventDispatcher $eventDispatcher
    ) {
        $this->graphqlTypesProvider = $graphqlTypesProvider;
        $this->graphqlDirectivesProvider = $graphqlDirectivesProvider;
        $this->graphqlSubfieldsCache = $graphqlSubfieldsCache;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $queries)
    {
        $rootQuery = $this->graphqlTypesProvider->getType('BuilderiusRootQuery');
        $rootMutation = $this->graphqlTypesProvider->getType('BuilderiusRootMutation');
        $this->graphqlSubfieldsCache->flush();

        $schema = new Schema([
            'query' => $rootQuery,
            'mutation' => $rootMutation,
            'directives' => $this->graphqlDirectivesProvider->getDirectives()
        ]);
        $results = [];
        foreach ($queries as $queryConfig) {
            $query = $queryConfig['query'];
            if (strpos($query, 'mutation') !== 0) {
                $event = new GraphQLQueryBeforeExecutionEvent($queryConfig['query']);
                $this->eventDispatcher->dispatch($event, 'builderius_graphql_query_before_execution');
                $query = $event->getQuery();
            }
            $result = GraphQL::executeQuery(
                $schema,
                $query,
                null,
                null,
                isset($queryConfig['variables']) ? $queryConfig['variables'] : null,
                null,
                [DefaultFieldResolver::class, 'resolve'],
                null,
                $this->graphqlSubfieldsCache,
                $this->eventDispatcher
            );
            $event = new GraphQLQueryExecutedEvent($queryConfig['query'], $result);
            $this->eventDispatcher->dispatch($event, 'builderius_graphql_query_executed');
            $result = $event->getResult();
            $errorMessages = [];
            foreach ($result->errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            $results[$queryConfig['name']] = ['data' => $result->data, 'errors' => $errorMessages];
        }

        return $results;
    }
}