<?php

namespace Builderius\Bundle\SettingBundle\Factory;

use Builderius\Bundle\SettingBundle\Model\BuilderiusGlobalSettingsSet;
use Builderius\Bundle\SettingBundle\Model\BuilderiusGlobalSettingsSetInterface;
use Builderius\Bundle\SettingBundle\Provider\DefaultSettingsValuesProvider;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusBranchFromPostFactory;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranchInterface;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusVCSTagTaxonomy;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusGlobalSettingsSetFromPostFactory
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusBranchFromPostFactory
     */
    private $branchFromPostFactory;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @var DefaultSettingsValuesProvider
     */
    private $defaultSettingsValuesProvider;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param \WP_Query $wpQuery
     * @param BuilderiusBranchFromPostFactory $branchFromPostFactory
     * @param BuilderiusRuntimeObjectCache $cache
     * @param DefaultSettingsValuesProvider $defaultSettingsValuesProvider
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        \WP_Query $wpQuery,
        BuilderiusBranchFromPostFactory $branchFromPostFactory,
        BuilderiusRuntimeObjectCache $cache,
        DefaultSettingsValuesProvider $defaultSettingsValuesProvider,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->wpQuery = $wpQuery;
        $this->branchFromPostFactory = $branchFromPostFactory;
        $this->cache = $cache;
        $this->defaultSettingsValuesProvider = $defaultSettingsValuesProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \WP_Post $post
     * @return BuilderiusGlobalSettingsSetInterface|null
     */
    public function createGlobalSettingsSet(\WP_Post $post)
    {
        $typeTechArr = explode('_', $post->post_name);

        return new BuilderiusGlobalSettingsSet([
            BuilderiusGlobalSettingsSet::ID_FIELD => $post->ID,
            BuilderiusGlobalSettingsSet::NAME_FIELD => $post->post_name,
            BuilderiusGlobalSettingsSet::TITLE_FIELD => 'Global Settings',
            BuilderiusGlobalSettingsSet::ENTITY_TYPE_FIELD => 'global_settings_set',
            BuilderiusGlobalSettingsSet::AUTHOR_FIELD => get_user_by('ID', $post->post_author),
            BuilderiusGlobalSettingsSet::CREATED_AT_FIELD => $post->post_date,
            BuilderiusGlobalSettingsSet::UPDATED_AT_FIELD => $post->post_modified,
            BuilderiusGlobalSettingsSet::ACTIVE_BRANCH_NAME_FIELD => function () use ($post) {
                $currUserId = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
                $activeBranchNameString = get_post_meta($post->ID, BuilderiusGlobalSettingsSet::ACTIVE_BRANCH_NAME_FIELD, true);
                $activeBranchName = 'master';
                $activeBranchNameJson = json_decode($activeBranchNameString, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if (isset($activeBranchNameJson[$currUserId])) {
                        $activeBranchName = $activeBranchNameJson[$currUserId];
                    }
                } elseif ($activeBranchNameString) {
                    $activeBranchName = $activeBranchNameString;
                }

                return $activeBranchName;
            },
            BuilderiusGlobalSettingsSet::TECHNOLOGY_FIELD => isset($typeTechArr[1]) ? $typeTechArr[0] : $post->post_name,
            BuilderiusGlobalSettingsSet::TYPE_FIELD => isset($typeTechArr[1]) ? $typeTechArr[1] : 'all',
            BuilderiusGlobalSettingsSet::DEFAULT_CONTENT_CONFIG_FIELD => function () use ($post) {
                $config = [
                    'template' => [
                        'type' => isset($typeTechArr[1]) ? $typeTechArr[1] : 'all',
                        'technology' => isset($typeTechArr[1]) ? $typeTechArr[0] : $post->post_name,
                        'settings' => $this->defaultSettingsValuesProvider->getDefaultValues(
                            'global',
                            isset($typeTechArr[1]) ? $typeTechArr[1] : 'all' ,
                            isset($typeTechArr[1]) ? $typeTechArr[0] : $post->post_name
                        )
                    ]
                ];
                $event = new ConfigContainingEvent($config);
                $this->eventDispatcher->dispatch($event, 'builderius_global_settings_set_default_not_committed_config_before_save');

                return $event->getConfig();
            },
            BuilderiusGlobalSettingsSet::INNER_COMMITS_TAGS_FIELD => function () use ($post) {
                global $wpdb;

                $sql = sprintf(
                    "SELECT DISTINCT t.name from %s t inner join %s tr on t.term_id = tr.term_taxonomy_id inner join %s tt on tr.term_taxonomy_id = tt.term_taxonomy_id join %s p on tr.object_id = p.ID where p.post_type = '%s' and tt.taxonomy = '%s' and p.post_parent IN (select ID from %s pp where pp.post_type = '%s' and pp.post_parent = %d) ",
                    $wpdb->terms,
                    $wpdb->term_relationships,
                    $wpdb->term_taxonomy,
                    $wpdb->posts,
                    BuilderiusCommitPostType::POST_TYPE,
                    BuilderiusVCSTagTaxonomy::NAME,
                    $wpdb->posts,
                    BuilderiusBranchPostType::POST_TYPE,
                    $post->ID
                );

                $results = $wpdb->get_results($sql);
                $tags = [];
                foreach ($results as $result) {
                    $tags[] = $result->name;
                }

                return $tags;
            },
            BuilderiusGlobalSettingsSet::BRANCHES_FIELD => function () use ($post) {
                $branches = $this->cache->get(sprintf('builderius_global_settings_set_%s_branches', $post->ID));
                if (false === $branches) {
                    $branches = [];
                    $branchesPosts = $this->cache->get(sprintf('builderius_global_settings_set_%s_branches_posts', $post->ID));
                    if (false === $branchesPosts) {
                        $branchesPosts = $this->wpQuery->query([
                            'post_type' => BuilderiusBranchPostType::POST_TYPE,
                            'post_parent' => $post->ID,
                            'post_status' => get_post_stati(),
                            'posts_per_page' => -1,
                            'no_found_rows' => true,
                            'orderby' => 'ID',
                            'order' => 'DESC'
                        ]);
                        $this->cache->set(sprintf('builderius_global_settings_set_%s_branches_posts', $post->ID), $branchesPosts);
                    }
                    /** @var BuilderiusBranchInterface[] $branches */
                    foreach ($branchesPosts as $branchPost) {
                        if (false === $this->cache->get(sprintf('builderius_branch_post_%s', $branchPost->ID))) {
                            $this->cache->set(sprintf('builderius_branch_post_%s', $branchPost->ID), $branchPost);
                        }
                        $branch = $this->branchFromPostFactory->createBranch($branchPost);
                        $this->cache->set(sprintf('builderius_branch_%s', $branchPost->ID), $branch);
                        $branches[$branchPost->post_name] = $branch;
                    }
                    $this->cache->set(sprintf('builderius_global_settings_set_%s_branches', $post->ID), $branches);
                }

                return $branches;
            }
        ]);
    }
}