<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;

class BuilderiusTemplateScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'template';

    /**
     * @var BuilderiusTemplateProviderInterface
     */
    private $builderiusTemplateProvider;

    /**
     * @var BuilderiusEntitiesGraphQLQueriesExecutorInterface
     */
    private $graphQLQueriesExecutor;

    /**
     * @param BuilderiusTemplateProviderInterface $builderiusTemplateProvider
     * @param BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor
     */
    public function __construct(
        BuilderiusTemplateProviderInterface $builderiusTemplateProvider,
        BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor
    ) {
        $this->builderiusTemplateProvider = $builderiusTemplateProvider;
        $this->graphQLQueriesExecutor = $graphQLQueriesExecutor;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $templatePost = $this->builderiusTemplateProvider->getTemplatePost();
        if ($templatePost) {
            $queries = [
                [
                    'name' => 'template',
                    'query' => 'query {
                            template(id: ' . $templatePost->ID . ') {
                                id
                                type
                                sub_type
                                technology
                                name
                                title
                                created_at
                                updated_at
                                builder_mode_link
                                default_content_config
                                active_branch_name
                                branches {
                                    id 
                                    name 
                                    active_commit_name
                                    commits {
                                        id 
                                        name
                                        description
                                        content_config
                                        created_at
                                        author {
                                            avatar_url
                                            display_name
                                        }
                                    }
                                }
                            }
                        }'
                ]
            ];
            $results = $this->graphQLQueriesExecutor->execute($queries);

            return $results['template']['data']['template'];
        }

        return null;
    }
}
