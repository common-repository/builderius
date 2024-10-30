<?php

namespace Builderius\Bundle\GraphQLBundle\Hook;

use Builderius\Bundle\GraphQLBundle\Event\GraphQLQueryBeforeExecutionEvent;
use Builderius\Bundle\GraphQLBundle\Event\GraphQLQueryExecutedEvent;
use Builderius\Bundle\GraphQLBundle\Provider\Directive\GraphQLDirectivesProviderInterface;
use Builderius\Bundle\GraphQLBundle\Provider\Type\GraphQLTypesProviderInterface;
use Builderius\Bundle\GraphQLBundle\Resolver\DefaultFieldResolver;
use Builderius\Bundle\GraphQLBundle\Type\RootTypeInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusPersistentObjectCache;
use Builderius\Bundle\TemplateBundle\Hook\AbstractApplicantDataAction;
use Builderius\GraphQL\Cache\GraphQLObjectCache;
use Builderius\GraphQL\GraphQL;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Schema;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;
use Builderius\Symfony\Component\Cache\CacheItem;

class ApplicantGraphQLDataVarsProvidingHook extends AbstractApplicantDataAction
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
     * @var BuilderiusPersistentObjectCache
     */
    private $persistentCache;

    /**
     * @param GraphQLTypesProviderInterface $graphqlTypesProvider
     * @return $this
     */
    public function setGraphQLTypesProvider(
        GraphQLTypesProviderInterface $graphqlTypesProvider
    ) {
        $this->graphqlTypesProvider = $graphqlTypesProvider;

        return $this;
    }

    /**
     * @param GraphQLDirectivesProviderInterface $graphqlDirectivesProvider
     * @return $this
     */
    public function setGraphqlDirectivesProvider(GraphQLDirectivesProviderInterface $graphqlDirectivesProvider)
    {
        $this->graphqlDirectivesProvider = $graphqlDirectivesProvider;

        return $this;
    }

    /**
     * @param GraphQLObjectCache $graphqlSubfieldsCache
     * @return $this
     */
    public function setGraphqlSubfieldsCache(GraphQLObjectCache $graphqlSubfieldsCache)
    {
        $this->graphqlSubfieldsCache = $graphqlSubfieldsCache;

        return $this;
    }

    /**
     * @param EventDispatcher $eventDispatcher
     * @return $this
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * @param BuilderiusPersistentObjectCache $persistentCache
     * @return $this
     */
    public function setPersistentCache(BuilderiusPersistentObjectCache $persistentCache)
    {
        $this->persistentCache = $persistentCache;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $user = apply_filters('builderius_get_current_user', $this->getUser());
        if (isset( $_POST['builderius-applicant-graphql-datavars']) && user_can($user, 'builderius-development')) {

            /** @var CacheItem $templateTypeCacheItem */
            $templateTypeCacheItem = $this->persistentCache->getItem('applicant_graphql_template_type');
            $templateType = $templateTypeCacheItem->get();
            $this->persistentCache->delete('applicant_graphql_template_type');
            $rootTypes = array_filter(
                $this->graphqlTypesProvider->getTypes(),
                function(Type $type) use ($templateType) {
                    if ($type instanceof RootTypeInterface &&
                        ($templateType === $type->getTemplateType() || ($templateType === 'all' && $type->isAppliedToAllTemplateTypes())
                        )
                    ) {
                        return true;
                    }
                    return false;
                }
            );
            if (empty($rootTypes)) {
                /** @var CacheItem $dataCacheItem */
                $dataCacheItem = $this->persistentCache->getItem('applicant_graphql_data');
                $dataCacheItem->set([]);
                $this->persistentCache->save($dataCacheItem);
            }
            /** @var RootTypeInterface|Type $rootType */
            $rootType = reset($rootTypes);
            $schema = new Schema([
                'query' => $rootType,
                'directives' => $this->graphqlDirectivesProvider->getDirectives()
            ]);
            $rootData = null;
            if ($rootDataProvider = $rootType->getRootDataProvider() ) {
                $rootData = $rootDataProvider->getRootData();
            }
            /** @var CacheItem $queriesCacheItem */
            $queriesCacheItem = $this->persistentCache->getItem('applicant_graphql_queries');
            $queries = is_array($queriesCacheItem->get()) ? $queriesCacheItem->get() : [];
            $this->persistentCache->delete('applicant_graphql_queries');
            $dataVars = [];
            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $applicantURL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            foreach ($queries as $queryConfig) {
                $event = new GraphQLQueryBeforeExecutionEvent($queryConfig['query']);
                $this->eventDispatcher->dispatch($event, 'builderius_graphql_query_before_execution');
                $query = $event->getQuery();
                $result = GraphQL::executeQuery(
                    $schema,
                    $query,
                    $rootData,
                    ['applicantUrl' => $applicantURL],
                    isset($queryConfig['variables']) ? $queryConfig['variables'] : null,
                    null,
                    [DefaultFieldResolver::class, 'resolve'],
                    null,
                    $this->graphqlSubfieldsCache,
                    $this->eventDispatcher
                );
                $event = new GraphQLQueryExecutedEvent($query, $result);
                $this->eventDispatcher->dispatch($event, 'builderius_graphql_query_executed');
                $result = $event->getResult();
                $dataVars[$queryConfig['name']] = $result->toArray();
            }
            /** @var CacheItem $dataCacheItem */
            $dataCacheItem = $this->persistentCache->getItem('applicant_graphql_data');
            $dataCacheItem->set($dataVars);
            $this->persistentCache->save($dataCacheItem);
        }
    }
}