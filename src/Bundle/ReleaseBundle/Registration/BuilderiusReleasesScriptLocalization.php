<?php

namespace Builderius\Bundle\ReleaseBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;

class BuilderiusReleasesScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'releases';

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
                            releases {
                                id
                                tag
                                description
                                status
                                sub_modules {
                                    id
                                    name
                                    type
                                    technology
                                    entity_type
                                }
                                created_at
                                author {
                                    display_name
                                }
                            }
                        }'
            ]
        ];
        $results = $this->graphQLQueriesExecutor->execute($queries);

        return $results[self::PROPERTY_NAME]['data'][self::PROPERTY_NAME];
    }
}
