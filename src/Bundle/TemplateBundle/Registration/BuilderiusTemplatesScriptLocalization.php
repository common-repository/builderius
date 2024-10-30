<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;

class BuilderiusTemplatesScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'templates';

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
                'name' => 'templates',
                'query' => 'query {
                            templates(type: template) {
                                id
                                type: sub_type
                                technology
                                name
                                title
                                sort_order
                                hook
                                hook_type
                                hook_accepted_args
                                apply_rules_config
                                created_at
                                updated_at
                                builder_mode_link
                                clear_existing_hooks
                                author {
                                  display_name
                                }
                            }
                        }'
            ]
        ];
        $results = $this->graphQLQueriesExecutor->execute($queries);

        $final = $results[self::PROPERTY_NAME]['data'][self::PROPERTY_NAME];
        if (null === $final) {
            $final = [];
        }

        return $final;
    }
}
