<?php

namespace Builderius\Bundle\SettingBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;

class BuilderiusGlobalSettingsScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'globalSettings';

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
        $template = $this->builderiusTemplateProvider->getTemplate();
        if ($template) {
            $queries = [
                [
                    'name' => static::PROPERTY_NAME,
                    'query' => 'query {
                            global_settings_sets(technology: ' . $template->getTechnology() . ') {
                                id
                                title
                                type
                                technology
                                active_branch_name
                                default_content_config
                                branches {
                                    id 
                                    name 
                                    active_commit_name
                                    owner {
                                        type
                                        technology
                                    }
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

            return $results[static::PROPERTY_NAME]['data']['global_settings_sets'][0];
        }

        return null;
    }
}
