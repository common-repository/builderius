<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\RestApi\Endpoint;

use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\TestingBundle\Tests\Functional\AbstractContainerAwareTestCase;

class BuilderiusGraphQLEndpointTest extends AbstractContainerAwareTestCase
{
    /**
     * @var BuilderiusEntitiesGraphQLQueriesExecutorInterface
     */
    private $executor;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->executor =
            $this->container->get('builderius_graphql.executor.builderius_entities_graphql_queries');
    }

    /*public function testProcessMutation()
    {
        $request = new \WP_REST_Request(
            'POST'
        );
        $request->add_header('content_type', 'application/json');
        $request->set_body(json_encode([
                'queries' => [
                    [
                        'name' => 'mutation1',
                        'query' => 'mutation {
                            createCommit(input: {branch_id: 1, serialized_content_config: "{\"modules\":{\"uae37f16c\":{\"id\":\"uae37f16c\",\"name\":\"BlockElement\",\"label\":\"BlockElement\",\"settings\":[$\"name\":\"backgroundColor\",\"value\":{\"all\":{\"original\":{\"a1\":\"rgba(41, 223, 165, 1.00)\"}}}},{\"name\":\"height\",\"value\":{\"all\":{\"original\":{\"b1\":\"px\",\"a1\":50}}}},{\"name\":\"htmlTagContainer\",\"value\":{\"a1\":\"div\"}},{\"name\":\"isLinkWrapper\",\"value\":{\"a1\":false}^],\"parent\":\"\"}},\"indexes\":{\"root\":[\"uae37f16c\"]},\"template\":{\"type\":\"singular\",\"technology\":\"html\",\"settings\":[$\"name\":\"responsiveStrategy\",\"value\":{\"a1\":\"desktop-first\"}^]}}", description: "test description"}) {
                                commit {
                                    ID
                                    name
                                    description
                                    content_config
                                    created_at
                                    branch {
                                        name
                                    }
                                }
                            }
                        }'
                    ]
                ]
            ])
    );
        $response = $this->endpoint->process($request);
    }*/

    /*public function testProcessTemplatesQuery()
    {
        $queries = [
            [
                'name' => 'query1',
                'query' => 'query {
                            templates {
                                id
                                name
                                master_branch: branch(name: "master") {
                                    id
                                    name
                                }
                                active_branch_name
                                active_branch {
                                    id
                                    name
                                    owner {
                                        id
                                        active_branch_name
                                    }
                                }
                                apply_rules_config
                                author {
                                    ID
                                }
                                branches {
                                    id
                                    name
                                    owner {
                                        id
                                        active_branch_name
                                        branches {
                                            id
                                            name
                                            owner {
                                                id
                                                active_branch_name
                                            }
                                            not_committed_config
                                            active_commit {
                                                id
                                                name
                                                description
                                            }
                                            commits {
                                                id
                                                name
                                                description
                                                branch {
                                                    id
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }'
            ]
        ];

        $response = $this->executor->execute($queries);
    }*/

    /*public function testProcessGlobalSettingsQuery()
    {
        $request = new \WP_REST_Request(
            'POST'
        );
        $request->add_header('content_type', 'application/json');
        $request->set_body(json_encode([
                'queries' => [
                    [
                        'name' => 'query1',
                        'query' => 'query {
                            global_settings_sets(type: singular) {
                                id
                                name
                                type
                                technology
                                master_branch: branch(name: "master") {
                                    id
                                    name
                                    owner {
                                        id
                                        active_branch_name
                                        active_branch {
                                            id
                                            name
                                        }
                                    }
                                }
                            }
                        }'
                    ]
                ]
            ])
        );

        $response = $this->endpoint->process($request);
    }*/

    /*public function testProcessCreateTemplate()
    {
        $queries = [
            [
                'name' => 'mutation1',
                'query' => 'mutation {
                    updateTemplate(input: {
                        id: 121
                        name: "from_graphql2",
                        title: "From Graphql2",
                    }) {
                        template {
                            id
                            name
                            type
                            technology
                            master_branch: branch(name: "master") {
                                id
                                name
                                owner {
                                    id
                                    active_branch_name
                                    active_branch {
                                        id
                                        name
                                    }
                                }
                            }
                        }    
                    }
                }'
            ]
        ];
        $response = $this->executor->execute($queries);
    }*/
    /*public function testProcessCreateBranch()
    {
        $queries = [
            [
                'name' => 'mutation1',
                'query' => 'mutation {
                    createBranch(input: {
                        owner_id: 27
                        name: "two"
                        serialized_not_committed_config: "{\"modules\":{\"uc3cc6ca0\":{\"id\":\"uc3cc6ca0\",\"name\":\"BlockElement\",\"label\":\"BlockElement\",\"settings\":[$\"name\":\"htmlTagContainer\",\"value\":{\"a1\":\"div\"}},{\"name\":\"isLinkWrapper\",\"value\":{\"a1\":false}^],\"parent\":\"\"}},\"indexes\":{\"root\":[\"uc3cc6ca0\"]},\"template\":{\"type\":\"singular\",\"technology\":\"html\",\"settings\":[$\"name\":\"responsiveStrategy\",\"value\":{\"a1\":\"desktop-first\"}^]}}",
                        base_branch_name: "master"
                        base_commit_name: "w232eqswdsqsqsqs",
                    }) {
                        branch {
                            id
                            name 
                            not_committed_config
                        } 
                    }
                }'
            ]
        ];
        $response = $this->executor->execute($queries);
    }*/
    /*public function testProcessUpdateBranch()
    {
        $queries = [
            [
                'name' => 'mutation1',
                'query' => 'mutation {
                    updateBranch(input: {
                        owner_id: 27
                        name: "two"
                        serialized_not_committed_config: "{\"modules\":{\"uc3cc6ca0\":{\"id\":\"uc3cc6ca0\",\"name\":\"BlockElement\",\"label\":\"BlockElement\",\"settings\":[$\"name\":\"htmlTagContainer\",\"value\":{\"a1\":\"div\"}},{\"name\":\"isLinkWrapper\",\"value\":{\"a1\":false}^],\"parent\":\"\"}},\"indexes\":{\"root\":[\"uc3cc6ca0\"]},\"template\":{\"type\":\"singular\",\"technology\":\"html\",\"settings\":[$\"name\":\"responsiveStrategy\",\"value\":{\"a1\":\"desktop-first\"}^]}}",
                        base_branch_name: "master"
                        base_commit_name: "w232eqswdsqsqsqs",
                    }) {
                        branch {
                            id
                            name 
                            not_committed_config
                        } 
                    }
                }'
            ]
        ];
        $response = $this->executor->execute($queries);
    }*/
    public function testProcessCreateRelease()
    {
        $queries = [
            [
                'name' => 'mutation1',
                'query' => 'mutation {
                    createRelease(input: {
                        description: "first release"
                        tag: "0.9.1"
                    }) {
                        release {
                            id
                            tag 
                            description
                        } 
                    }
                }'
            ]
        ];
        $response = $this->executor->execute($queries);
    }
}