<?php

namespace Builderius\Bundle\TemplateBundle\Factory;

use Builderius\Bundle\SettingBundle\Provider\DefaultSettingsValuesProvider;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateInterface;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateSubTypeTaxonomy;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusBranchFromPostFactory;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranchInterface;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTypeTaxonomy;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusVCSTagTaxonomy;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusTemplateFromPostFactory
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
     * @var BuilderiusTemplateTypesProviderInterface
     */
    private $templateTypesProvider;

    /**
     * @param \WP_Query $wpQuery
     * @param BuilderiusBranchFromPostFactory $branchFromPostFactory
     * @param BuilderiusRuntimeObjectCache $cache
     * @param DefaultSettingsValuesProvider $defaultSettingsValuesProvider
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     */
    public function __construct(
        \WP_Query $wpQuery,
        BuilderiusBranchFromPostFactory $branchFromPostFactory,
        BuilderiusRuntimeObjectCache $cache,
        DefaultSettingsValuesProvider $defaultSettingsValuesProvider,
        EventDispatcherInterface $eventDispatcher,
        BuilderiusTemplateTypesProviderInterface $templateTypesProvider
    ) {
        $this->wpQuery = $wpQuery;
        $this->branchFromPostFactory = $branchFromPostFactory;
        $this->cache = $cache;
        $this->defaultSettingsValuesProvider = $defaultSettingsValuesProvider;
        $this->eventDispatcher = $eventDispatcher;
        $this->templateTypesProvider = $templateTypesProvider;
    }


    /**
     * @param \WP_Post $post
     * @return BuilderiusTemplateInterface|null
     */
    public function createTemplate(\WP_Post $post)
    {
        $typeTerms = get_the_terms($post->ID, BuilderiusTemplateTypeTaxonomy::NAME);
        $typeTerm = !empty($typeTerms) ? reset($typeTerms)->slug : null;
        $type = $this->templateTypesProvider->getType($typeTerm);
        if ($type && $type->isStandalone()) {
            $entityType = 'template';
        } else {
            $event = new ConfigContainingEvent(['type' => $typeTerm, 'entity_type' => 'template']);
            $this->eventDispatcher->dispatch($event, 'builderius_template_entity_type_setting');
            $config = $event->getConfig();
            $entityType = $config['entity_type'];
        }
        $subTypeTerms = get_the_terms($post->ID, BuilderiusTemplateSubTypeTaxonomy::NAME);
        $subTypeTerm = !empty($subTypeTerms) ? reset($subTypeTerms)->slug : 'default';
        $technologyTerms = get_the_terms($post->ID, BuilderiusTemplateTechnologyTaxonomy::NAME);
        $technologyTerm = !empty($technologyTerms) ? reset($technologyTerms)->slug : null;
        $contentRenderHook = get_post_meta($post->ID, BuilderiusTemplate::HOOK_FIELD, true);
        $contentRenderHookType = get_post_meta($post->ID, BuilderiusTemplate::HOOK_TYPE_FIELD, true);
        $contentRenderHookAccArgs = get_post_meta($post->ID, BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD, true);
        $clearExistingHooks = get_post_meta($post->ID, BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD, true);

        return new BuilderiusTemplate([
            BuilderiusTemplate::ID_FIELD => $post->ID,
            BuilderiusTemplate::NAME_FIELD => $post->post_name,
            BuilderiusTemplate::TITLE_FIELD => $post->post_title,
            BuilderiusTemplate::HOOK_FIELD => $contentRenderHook ? : null,
            BuilderiusTemplate::HOOK_TYPE_FIELD => $contentRenderHookType ? : null,
            BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD => $contentRenderHookAccArgs ? : null,
            BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD => $clearExistingHooks === 'true' ,
            BuilderiusTemplate::ENTITY_TYPE_FIELD => $entityType,
            BuilderiusTemplate::AUTHOR_FIELD => get_user_by('ID', $post->post_author),
            BuilderiusTemplate::CREATED_AT_FIELD => $post->post_date,
            BuilderiusTemplate::UPDATED_AT_FIELD => $post->post_modified,
            BuilderiusTemplate::ACTIVE_BRANCH_NAME_FIELD => function () use ($post) {
                $currUserId = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
                $activeBranchNameString = get_post_meta($post->ID, BuilderiusTemplate::ACTIVE_BRANCH_NAME_FIELD, true);
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
            BuilderiusTemplate::SORT_ORDER_FIELD => get_post_meta($post->ID, BuilderiusTemplate::SORT_ORDER_FIELD, true),
            BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD =>
                json_decode(get_post_meta($post->ID, BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD, true), true),
            BuilderiusTemplate::TYPE_FIELD => $typeTerm,
            BuilderiusTemplate::SUB_TYPE_FIELD => $subTypeTerm,
            BuilderiusTemplate::TECHNOLOGY_FIELD => $technologyTerm,
            BuilderiusTemplate::BUILDER_MODE_LINK_FIELD => self::generateBuilderModeLink($post),
            BuilderiusTemplate::DEFAULT_CONTENT_CONFIG_FIELD => function () use ($typeTerm, $technologyTerm) {
                $config = [
                    'template' => [
                        'type' => $typeTerm,
                        'technology' => $technologyTerm,
                        'settings' => $this->defaultSettingsValuesProvider->getDefaultValues(
                            'template',
                            $typeTerm ,
                            $technologyTerm
                        )
                    ]
                ];
                $event = new ConfigContainingEvent($config);
                $this->eventDispatcher->dispatch($event, 'builderius_template_default_not_committed_config_before_save');

                return $event->getConfig();
            },
            BuilderiusTemplate::INNER_COMMITS_TAGS_FIELD => function () use ($post) {
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
            BuilderiusTemplate::BRANCHES_FIELD => function () use ($post) {
                $branches = $this->cache->get(sprintf('builderius_template_%s_branches', $post->ID));
                if (false === $branches) {
                    $branches = [];
                    $branchesPosts = $this->cache->get(sprintf('builderius_template_%s_branches_posts', $post->ID));
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
                        $this->cache->set(sprintf('builderius_template_%s_branches_posts', $post->ID), $branchesPosts);
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
                    $this->cache->set(sprintf('builderius_template_%s_branches', $post->ID), $branches);
                }

                return $branches;
            }
        ]);
    }

    /**
     * @param \WP_Post $post
     * @return string
     */
    public static function generateBuilderModeLink(\WP_Post $post)
    {
        $permalink = get_permalink($post->ID);

        return strpos($permalink, '?') !== false ?
            sprintf('%s&builderius', $permalink) :
            sprintf('%s?builderius', $permalink);
    }
}
