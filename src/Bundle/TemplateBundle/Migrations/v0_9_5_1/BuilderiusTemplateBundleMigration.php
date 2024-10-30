<?php

namespace Builderius\Bundle\TemplateBundle\Migrations\v0_9_5_1;

use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\MooMoo\Platform\Bundle\MigrationBundle\Migration\Migration;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareTrait;

class BuilderiusTemplateBundleMigration implements Migration, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var BuilderiusEntitiesGraphQLQueriesExecutorInterface
     */
    private $graphQLQueriesExecutor;

    /**
     * @return BuilderiusEntitiesGraphQLQueriesExecutorInterface
     */
    private function getGraphQLQueriesExecutor()
    {
        if ($this->graphQLQueriesExecutor === null) {
            $this->graphQLQueriesExecutor = $this->container->get('builderius_graphql.executor.builderius_entities_graphql_queries');
        }

        return $this->graphQLQueriesExecutor;
    }

    /**
     * @inheritDoc
     */
    public function up(\wpdb $wpdb)
    {
        $queries = [
            [
                'name' => 'releases',
                'query' => 'query {
                            releases {
                                id
                                tag
                                description
                                sub_modules {
                                    id
                                    name
                                    type
                                    technology
                                    entity_type
                                }
                            }
                        }'
            ]
        ];
        $results = $this->getGraphQLQueriesExecutor()->execute($queries);
        if (isset($results['releases']['data']['releases'])) {
            $releases = $results['releases']['data']['releases'];
            if (is_array($releases)) {
                foreach ($releases as $release) {
                    if (
                        isset($release['tag']) && $release['tag'] === '1.0.0' &&
                        isset($release['description']) && $release['description'] === 'automatically generated release'
                    ) {
                        $justGlobalSettingsInSubModules = true;
                        if (isset($release['sub_modules']) && is_array($release['sub_modules'])) {
                            foreach ($release['sub_modules'] as $subModule) {
                                if (isset($subModule['entity_type']) && $subModule['entity_type'] !== 'global_settings_set') {
                                    $justGlobalSettingsInSubModules = false;
                                    break;
                                }
                            }
                        }
                        if ($justGlobalSettingsInSubModules === true) {
                            foreach ($release['sub_modules'] as $subModule) {
                                if (isset($subModule['id'])) {
                                    wp_delete_post($subModule['id'], true);
                                }
                            }
                            wp_delete_post($release['id'], true);
                        }
                    }
                }
            }
        }
    }
}