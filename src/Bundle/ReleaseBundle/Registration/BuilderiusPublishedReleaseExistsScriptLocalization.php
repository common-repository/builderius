<?php

namespace Builderius\Bundle\ReleaseBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;

class BuilderiusPublishedReleaseExistsScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'publishedReleaseExists';

    /**
     * @var BuilderiusEntitiesGraphQLQueriesExecutorInterface
     */
    private $graphQLQueriesExecutor;

    /**
     * @param BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor
     */
    public function __construct(BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor)
    {
        $this->graphQLQueriesExecutor = $graphQLQueriesExecutor;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $queries = [
            [
                'name' => 'releases',
                'query' => 'query {
                            releases(status: publish) {
                                id
                            }
                        }'
            ]
        ];
        $results = $this->graphQLQueriesExecutor->execute($queries);

        return !empty($results['releases']['data']['releases']) ? "true" : "false";
    }
}
