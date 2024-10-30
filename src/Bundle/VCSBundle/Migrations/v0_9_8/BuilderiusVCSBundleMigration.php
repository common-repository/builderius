<?php

namespace Builderius\Bundle\VCSBundle\Migrations\v0_9_8;

use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\MooMoo\Platform\Bundle\MigrationBundle\Migration\Migration;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareTrait;

class BuilderiusVCSBundleMigration implements Migration, ContainerAwareInterface
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
                'name' => 'branches',
                'query' => 'query {
                            branches {
                                id
                                not_committed_config
                            }
                        }'
            ]
        ];
        $results = $this->getGraphQLQueriesExecutor()->execute($queries);
        if (isset($results['branches']['data']['branches'])) {
            $branches = $results['branches']['data']['branches'];
            if (is_array($branches)) {
                $mutations = [];
                foreach ($branches as $i => $branch) {
                    if (is_array($branch['not_committed_config'])) {
                        $ncc = json_encode($branch['not_committed_config'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
                        $ncc = str_replace('\\', '\\\\', $ncc);
                        $ncc = str_replace('"', '\\"', $ncc);

                        $mutations[] =
                            [
                                'name' => 'createCommit' . $i,
                                'query' => 'mutation {
                                            createCommit(input: {
                                                branch_id: ' . $branch['id'] . ', 
                                                serialized_content_config: "' . $ncc . '", 
                                                description: "Revision"
                                                }) {
                                                    commit {
                                                        id
                                                    }
                                                }    
                                            }'
                            ];
                    }
                }
                $this->getGraphQLQueriesExecutor()->execute($mutations);
            }
        }
    }
}