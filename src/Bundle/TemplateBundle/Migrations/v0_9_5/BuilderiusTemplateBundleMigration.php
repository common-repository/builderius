<?php

namespace Builderius\Bundle\TemplateBundle\Migrations\v0_9_5;

use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\ReleaseBundle\Registration\BulderiusReleasePostType;
use Builderius\Bundle\SettingBundle\Registration\BuilderiusGlobalSettingsSetPostType;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\Bundle\TemplateBundle\Converter\Version\CompositeBuilderiusTemplateConfigVersionConverter;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Helper\ContentConfigHelper;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranch;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommit;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchHeadCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusVCSTagTaxonomy;
use Builderius\MooMoo\Platform\Bundle\MigrationBundle\Migration\Migration;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusTemplateBundleMigration implements Migration, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var BuilderiusEntitiesGraphQLQueriesExecutorInterface
     */
    private $graphQLQueriesExecutor;

    private function getWpQuery()
    {
        if ($this->wpQuery === null) {
            $this->wpQuery = $this->container->get('moomoo_query.wp_query');
        }

        return $this->wpQuery;
    }

    /**
     * @return EventDispatcherInterface
     */
    private function getEventDispatcher()
    {
        if ($this->eventDispatcher === null) {
            $this->eventDispatcher = $this->container->get('event_dispatcher');
        }

        return $this->eventDispatcher;
    }

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
     * @return BuilderiusTemplateConfigVersionConverterInterface[]
     */
    private function getBuilderiusTemplateConfigVersionConverters()
    {
        /** @var CompositeBuilderiusTemplateConfigVersionConverter $compositeConverter */
        $compositeConverter = $this->container->get('builderius_template.version_converter.composite');

        return $compositeConverter->getConverters('builderius', '0.9.5');
    }

    /**
     * @inheritDoc
     */
    public function up(\wpdb $wpdb)
    {
        $posts = $wpdb->get_results(
            "SELECT ID, post_name, post_type FROM {$wpdb->posts} WHERE post_type IN ('builderius_branch', 'builderius_commit') AND post_content != 'null' AND post_content IS NOT NULL");

        $tagTermCreated = false;
        foreach ($posts as $post) {
            if ($post->post_type === BuilderiusBranchPostType::POST_TYPE) {
                $config = ContentConfigHelper::formatConfig(
                    json_decode(
                        get_post_meta( $post->ID, BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD, true ),
                        true
                    )
                );
            } else {
                $config = ContentConfigHelper::formatConfig(
                    json_decode(
                        get_post_meta( $post->ID, BuilderiusCommit::CONTENT_CONFIG_FIELD, true ),
                        true
                    )
                );
            }
            if (is_array($config) && (!isset($config['version']) || !isset($config['version']['builderius']) || version_compare($config['version']['builderius'], '0.9.5') === -1)) {
                foreach ($this->getBuilderiusTemplateConfigVersionConverters() as $versionConverter) {
                    $config = $versionConverter->convert($config);
                }
                if (!isset($config['version'])) {
                    $config['version'] = [];
                }
                $currUserId = get_current_user_id();
                $config['version']['builderius'] = '0.9.5';
                if ($post->post_type === BuilderiusBranchPostType::POST_TYPE) {
                    delete_post_meta( $post->ID, BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD);
                    $post->post_content = null;
                    $branchId = wp_update_post($post);
                    if (isset($config['template']) || isset($config['modules'])) {
                        $nccBaseCommitName = null;
                        $commitsPosts = $this->getWpQuery()->query([
                            'post_type' => BuilderiusCommitPostType::POST_TYPE,
                            'post_parent' => $branchId,
                            'post_status' => get_post_stati(),
                            'posts_per_page' => 1,
                            'orderby' => 'ID',
                            'order' => 'DESC'
                        ]);
                        if (!empty($commitsPosts)) {
                            $nccBaseCommitName = $commitsPosts[0]->post_name;
                        }
                        $branchHeadCommitPost = $this->createBranchHeadCommitPost($branchId, $currUserId);
                        update_post_meta(
                            $branchHeadCommitPost->ID,
                            BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD,
                            wp_slash(json_encode($config))
                        );
                        if ($nccBaseCommitName !== null) {
                            update_post_meta(
                                $branchHeadCommitPost->ID,
                                BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD,
                                $nccBaseCommitName
                            );
                        }
                        $this->getEventDispatcher()->dispatch(new PostContainingEvent($branchHeadCommitPost), 'builderius_branch_head_commit_created');
                    }
                } else {
                    update_post_meta(
                        $post->ID,
                        BuilderiusCommit::CONTENT_CONFIG_FIELD,
                        wp_slash(json_encode($config))
                    );
                    $post = get_post($post->ID);
                    $this->getEventDispatcher()->dispatch(new PostContainingEvent($post), 'builderius_commit_created');
                    $branchPublishedCommit = get_post_meta($post->post_parent, 'published_commit', true);
                    if ($branchPublishedCommit && $branchPublishedCommit === $post->post_name) {
                        if ($tagTermCreated === false) {
                            wp_insert_term('1.0.0', BuilderiusVCSTagTaxonomy::NAME);
                            $tagTermCreated = true;
                        }
                        wp_set_post_terms($post->ID, ['1.0.0'], BuilderiusVCSTagTaxonomy::NAME, true);
                        delete_post_meta($post->post_parent, 'published_commit');
                    }
                }
            }
        }
        $settingsConfig = get_option('builderius_global_settings');
        if ($settingsConfig) {
            $newSettingsConfig = [
                'singular' => [
                    'template' => [
                        'type' => 'singular',
                        'technology' => 'html',
                        'settings' => []
                    ]
                ],
                'all' => [
                    'template' => [
                        'type' => 'all',
                        'technology' => 'html',
                        'settings' => []
                    ]
                ]
            ];
            $settingsConfig = json_decode($settingsConfig, true);
            if (isset($settingsConfig['html']) && isset($settingsConfig['html']['settings'])) {
                foreach ($settingsConfig['html']['settings'] as $settingConfig) {
                    foreach ($settingConfig['value'] as $type => $value) {
                        $newSettingsConfig[$type]['template']['settings'][] = [
                            'name' => $settingConfig['name'],
                            'value' => $value
                        ];
                    }
                }
            }
            foreach ($newSettingsConfig as $type => $config) {
                foreach ($this->getBuilderiusTemplateConfigVersionConverters() as $versionConverter) {
                    $config = $versionConverter->convert($config);
                }
                if (!isset($config['version'])) {
                    $config['version'] = [];
                }
                $config['version']['builderius'] = '0.9.5';
                $newSettingsConfig[$type] = $config;
            }
            $this->createGlobalSettingsSetPost('singular', 'html', $newSettingsConfig['singular'], $tagTermCreated);
            $this->createGlobalSettingsSetPost('all', 'html', $newSettingsConfig['all'], $tagTermCreated);
            delete_option('builderius_global_settings');
        }
        if (!empty($posts)) {
            $existingReleases = $this->getWpQuery()->query([
                'post_type' => BulderiusReleasePostType::POST_TYPE,
                'post_name' => '1.0.0',
                'posts_per_page' => 1,
                'orderby' => 'ID',
                'order' => 'DESC'
            ]);
            if (empty($existingReleases)) {
                $queries = [
                    [
                        'name' => 'createRelease',
                        'query' => 'mutation {
                    createRelease(input: {
                        description: "automatically generated release"
                        tag: "1.0.0"
                    }) {
                        release {
                            id
                        } 
                    }
                }'
                    ]
                ];
                $results = $this->getGraphQLQueriesExecutor()->execute($queries);
                if (is_array($results) && isset($results['createRelease']['data']['createRelease']['release']['id'])) {
                    $releaseId = $results['createRelease']['data']['createRelease']['release']['id'];
                    $queries = [
                        [
                            'name' => 'publishRelease',
                            'query' => 'mutation {
                    publishRelease(id: ' . $releaseId . ') {
                        release {
                            id
                            status
                        } 
                    }
                }'
                        ]
                    ];
                    $this->getGraphQLQueriesExecutor()->execute($queries);
                }
            }
        }
    }

    /**
     * @param int $postId
     * @param int $currUserId
     * @return \WP_Post
     */
    protected function createBranchHeadCommitPost($postId, $currUserId)
    {
        $branchHeadCommitPost = new \stdClass();
        $branchHeadCommitPost->post_name = sprintf('branch_%d_user_%d', $postId, $currUserId);
        $branchHeadCommitPost->post_type = BuilderiusBranchHeadCommitPostType::POST_TYPE;
        $branchHeadCommitPost->post_parent = $postId;
        $branchHeadCommitPost->post_author = $currUserId;
        $branchHeadCommitPostId = wp_insert_post(wp_slash((array)$branchHeadCommitPost), true);

        return get_post($branchHeadCommitPostId);
    }

    /**
     * @param string $type
     * @param string $technology
     * @param array $config
     * @param bool $setTag
     */
    private function createGlobalSettingsSetPost($type, $technology, array $config, $setTag)
    {
        $existingGlobalSettingsSetPosts = $this->getWpQuery()->query([
            'post_type' => BuilderiusGlobalSettingsSetPostType::POST_TYPE,
            'post_status' => get_post_stati(),
            'name' => sprintf('%s_%s', $technology, $type),
        ]);
        if (empty($existingGlobalSettingsSetPosts)) {
            $currUserId = get_current_user_id();
            $time = current_time('mysql');
            $globalSettingsSetArguments = [
                'post_name' => sprintf('%s_%s', $technology, $type),
                'post_type' => BuilderiusGlobalSettingsSetPostType::POST_TYPE,
                'post_author' => $currUserId,
                'post_date' => $time,
                'post_date_gmt' => get_gmt_from_date($time),
            ];
            $globalSettingsSetPostId = wp_insert_post(wp_slash($globalSettingsSetArguments), true);
            if (!is_wp_error($globalSettingsSetPostId)) {
                $branchArguments = [
                    'post_name' => 'master',
                    'post_parent' => $globalSettingsSetPostId,
                    'post_type' => BuilderiusBranchPostType::POST_TYPE,
                    'post_author' => $currUserId,
                    'post_date' => $time,
                    'post_date_gmt' => get_gmt_from_date($time),
                ];
                $branchPostId = wp_insert_post(wp_slash($branchArguments), true);
                if (!is_wp_error($branchPostId) && !empty($config['template']['settings'])) {
                    if (count($config['template']['settings']) > 1 || $config['template']['settings'][0]['name'] !== 'responsiveStrategy') {
                        $preparedCommitPost = new \stdClass;
                        $event = new ConfigContainingEvent($config);
                        $this->getEventDispatcher()->dispatch($event, 'builderius_commit_content_config_before_save');
                        $config = $event->getConfig();
                        $preparedCommitPost->{BuilderiusCommit::CONTENT_CONFIG_FIELD} = json_encode($config);
                        $preparedCommitPost->post_parent = (int)$branchPostId;
                        $preparedCommitPost->post_name = bin2hex(random_bytes(10));
                        $preparedCommitPost->post_excerpt = 'initial commit';
                        $preparedCommitPost->post_type = BuilderiusCommitPostType::POST_TYPE;
                        $preparedCommitPost->post_status = 'draft';
                        $preparedCommitPost->post_author = get_current_user_id();
                        $time = current_time('mysql');
                        $preparedCommitPost->post_date = $time;
                        $preparedCommitPost->post_date_gmt = get_gmt_from_date($time);
                        $postId = wp_insert_post(wp_slash((array)$preparedCommitPost), true);
                        if (is_wp_error($postId)) {
                            /** @var \WP_Error $postId */
                            if ('db_insert_error' === $postId->get_error_code()) {
                                throw new \Exception($postId->get_error_message(), 500);
                            } else {
                                throw new \Exception($postId->get_error_message(), 400);
                            }
                        }
                        $post = get_post($postId);
                        if (property_exists($preparedCommitPost, BuilderiusCommit::CONTENT_CONFIG_FIELD)) {
                            update_post_meta(
                                $postId,
                                BuilderiusCommit::CONTENT_CONFIG_FIELD,
                                wp_slash($preparedCommitPost->{BuilderiusCommit::CONTENT_CONFIG_FIELD})
                            );
                        }
                        $this->eventDispatcher->dispatch(new PostContainingEvent($post), 'builderius_commit_created');
                        if ($setTag) {
                            wp_set_post_terms($post->ID, ['1.0.0'], BuilderiusVCSTagTaxonomy::NAME, true);
                        }
                    }
                }
            }
        }
    }
}