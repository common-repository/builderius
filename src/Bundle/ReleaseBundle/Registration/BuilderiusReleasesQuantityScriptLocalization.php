<?php

namespace Builderius\Bundle\ReleaseBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;

class BuilderiusReleasesQuantityScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'releasesQuantity';

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
                'name' => 'releasesQuantity',
                'query' => 'query {
                            releases {
                                id
                            }
                        }'
            ]
        ];
        $results = $this->graphQLQueriesExecutor->execute($queries);

        return count($results[self::PROPERTY_NAME]['data']['releases']);
    }
}
