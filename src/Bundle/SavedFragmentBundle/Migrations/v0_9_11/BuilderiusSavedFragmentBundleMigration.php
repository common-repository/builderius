<?php

namespace Builderius\Bundle\SavedFragmentBundle\Migrations\v0_9_11;

use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\SavedFragmentBundle\Model\BuilderiusSavedFragment;
use Builderius\MooMoo\Platform\Bundle\MigrationBundle\Migration\Migration;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareTrait;

class BuilderiusSavedFragmentBundleMigration implements Migration, ContainerAwareInterface
{
    use ContainerAwareTrait;

    const DEFAULT_LAYOUTS = [
        'layout_content_1' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_content_1.jpg',
        'layout_content_2' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_content_2.jpg',
        'layout_content_3' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_content_3.jpg',
        'layout_content_4' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_content_4.jpg',
        'layout_content_5' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_content_5.png',
        'layout_content_6' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_content_6.png',
        'layout_content_7' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_content_7.png',
        'layout_footer_1' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_footer_1.png',
        'layout_footer_2' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_footer_2.png',
        'layout_form_1' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_form_1.png',
        'layout_header_1' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_header_1.png',
        'layout_header_2' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_header_2.png',
        'layout_header_3' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_header_3.png',
        'layout_hero_1' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_hero_1.png',
        'layout_hero_2' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_hero_2.png',
        'layout_hero_3' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_hero_3.png',
        'layout_hero_4' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_hero_4.jpg',
        'layout_our_partners_1' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_our_partners_1.png',
        'layout_subscribe_1' => 'https://showcase.builderius.io/wp-content/uploads/2023/02/layout_subscribe_1.png',
    ];

    /**
     * @inheritDoc
     */
    public function up(\wpdb $wpdb)
    {
        /** @var BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor */
        $graphQLQueriesExecutor = $this->container->get('builderius_graphql.executor.builderius_entities_graphql_queries');
        $queries = [
            [
                'name' => 'saved_fragments',
                'query' => 'query {
                                layouts: saved_fragments(type: layout, technology: html) {
                                    id
                                    name
                                }
                            }'
            ]
        ];

        $results = $graphQLQueriesExecutor->execute($queries);

        $data = $results['saved_fragments']['data'];
        foreach ($data['layouts'] as $layout) {
            if (in_array($layout['name'], array_keys(self::DEFAULT_LAYOUTS))) {
                update_post_meta(
                    $layout['id'],
                    BuilderiusSavedFragment::IMAGE_FIELD,
                    self::DEFAULT_LAYOUTS[$layout['name']]
                );
            }
        }
    }
}