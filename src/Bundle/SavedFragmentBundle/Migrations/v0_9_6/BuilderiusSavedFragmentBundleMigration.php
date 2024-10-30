<?php

namespace Builderius\Bundle\SavedFragmentBundle\Migrations\v0_9_6;

use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\LayoutBundle\Provider\StandardBuilderiusLayoutsProvider;
use Builderius\MooMoo\Platform\Bundle\MigrationBundle\Migration\Migration;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareTrait;

class BuilderiusSavedFragmentBundleMigration implements Migration, ContainerAwareInterface
{
    use ContainerAwareTrait;

    const CATEGORIES = [
        [
            'name' => 'general',
            'label' => 'General'
        ],
        [
            'name' => 'headers',
            'label' => 'Headers'
        ],
        [
            'name' => 'footers',
            'label' => 'Footers'
        ],
        [
            'name' => 'hero_sections',
            'label' => 'Hero Sections'
        ],
        [
            'name' => 'content_sections',
            'label' => 'Content Sections'
        ],
        [
            'name' => 'our_team_sections',
            'label' => 'Our team/partners sections'
        ]
    ];

    /**
     * @inheritDoc
     */
    public function up(\wpdb $wpdb)
    {
        /** @var StandardBuilderiusLayoutsProvider $layoutsProvider */
        $layoutsProvider = $this->container->get('builderius_layout.provider.standard');
        /** @var BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor */
        $graphQLQueriesExecutor = $this->container->get('builderius_graphql.executor.builderius_entities_graphql_queries');
        $queries = [];
        foreach (self::CATEGORIES as $k => $category) {
            $queries[] = [
                'name' => sprintf('query%d', $k),
                'query' => "mutation {
                             createCategory(input: {
                               name: \"" . $category['name'] . "\",
                               label: \"" . $category['label'] . "\",
                               groups: ". json_encode(['saved_fragment']) .",
                             }) {
                               category {
                                 id
                               }
                             }
                           }"
            ];
        }
        $graphQLQueriesExecutor->execute($queries);
        $queries = [];
        foreach ($layoutsProvider->getLayouts('html') as $i => $layout) {
            $queries[] = [
                'name' => sprintf('query%d', $i),
                'variables' => [
                    'description' => addslashes($layout->getDescription())
                ],
                'query' => "mutation(\$description: String) {
                              createSavedFragment(input: {
                                name: \"". $layout->getName() ."\",
                                title: \"". $layout->getLabel() ."\",
                                technology: html,
                                type: layout,
                                category: \"". $layout->getCategory() ."\",
                                tags: ". json_encode($layout->getTags()) .",
                                image: \"". $layout->getImage() ."\",
                                description: \$description,
                                serialized_static_content_config: \"". str_replace("\'", "'", addslashes(json_encode($layout->getConfig()))) ."\",
                                replace: true
                              }) {
                                saved_fragment {
                                  id
                                }
                              }
                           }"
            ];
        }
        $graphQLQueriesExecutor->execute($queries);
    }
}