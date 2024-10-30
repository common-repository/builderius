<?php

namespace Builderius\Bundle\VCSBundle\Factory;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Helper\ContentConfigHelper;
use Builderius\Bundle\VCSBundle\Event\BuilderiusVCSOwnerFromPostCreationEvent;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranch;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranchInterface;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommitInterface;
use Builderius\Bundle\VCSBundle\Model\BuilderiusVCSOwnerInterface;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchHeadCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusVCSTagTaxonomy;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;

class BuilderiusBranchFromPostFactory
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusCommitFromPostFactory
     */
    private $commitFromPostFactory;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param \WP_Query $wpQuery
     * @param BuilderiusCommitFromPostFactory $commitFromPostFactory
     * @param BuilderiusRuntimeObjectCache $cache
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        \WP_Query $wpQuery,
        BuilderiusCommitFromPostFactory $commitFromPostFactory,
        BuilderiusRuntimeObjectCache $cache,
        EventDispatcher $eventDispatcher
    ) {
        $this->wpQuery = $wpQuery;
        $this->commitFromPostFactory = $commitFromPostFactory;
        $this->cache = $cache;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \WP_Post $post
     * @return BuilderiusBranchInterface
     */
    public function createBranch(\WP_Post $post)
    {
        $currentUser = apply_filters('builderius_get_current_user', wp_get_current_user());
        $currUserId = $currentUser->ID;
        $baseBranchName = get_post_meta($post->ID, BuilderiusBranch::BASE_BRANCH_NAME_FIELD, true) ? : null;
        $baseCommitName = get_post_meta($post->ID, BuilderiusBranch::BASE_COMMIT_NAME_FIELD, true) ? : null;

        return new BuilderiusBranch([
            BuilderiusBranch::ID_FIELD => $post->ID,
            BuilderiusBranch::OWNER_ID_FIELD => $post->post_parent,
            BuilderiusBranch::AUTHOR_FIELD => get_user_by('ID', $post->post_author),
            BuilderiusBranch::NAME_FIELD => $post->post_name,
            BuilderiusBranch::CREATED_AT_FIELD => $post->post_date,
            BuilderiusBranch::UPDATED_AT_FIELD => $post->post_modified,
            BuilderiusBranch::BASE_BRANCH_NAME_FIELD => $baseBranchName,
            BuilderiusBranch::BASE_BRANCH_FIELD => function () use ($post, $baseBranchName) {
                $baseBranch = $this->cache->get(sprintf('builderius_branch_%s_base_branch', $post->ID));
                if (false === $baseBranch) {
                    $baseBranch = null;
                    if ($baseBranchName) {
                        $branchPosts = $this->wpQuery->query([
                            'name' => $baseBranchName,
                            'post_type' => BuilderiusBranchPostType::POST_TYPE,
                            'post_parent' => $post->post_parent,
                            'post_status' => get_post_stati(),
                            'posts_per_page' => 1,
                            'no_found_rows' => true,
                        ]);
                        if (!empty($branchPosts)) {
                            $branchPost = reset($branchPosts);
                            $baseBranch = $this->createBranch($branchPost);

                        }
                    }
                    $this->cache->set(sprintf('builderius_branch_%s_base_branch', $post->ID), $baseBranch);
                }
                return $baseBranch;
            },
            BuilderiusBranch::BASE_COMMIT_NAME_FIELD => $baseCommitName,
            BuilderiusBranch::BASE_COMMIT_FIELD => function () use ($post, $baseCommitName) {
                $baseCommit = $this->cache->get(sprintf('builderius_branch_%s_base_commit', $post->ID));
                if (false === $baseCommit) {
                    $baseCommit = null;
                    if ($baseCommitName) {
                        $commitPosts = $this->wpQuery->query([
                            'name' => $baseCommitName,
                            'post_type' => BuilderiusCommitPostType::POST_TYPE,
                            'post_status' => get_post_stati(),
                            'posts_per_page' => 1,
                            'no_found_rows' => true,
                        ]);
                        if (!empty($commitPosts)) {
                            $commitPost = reset($commitPosts);
                            $baseCommit = $this->commitFromPostFactory->createCommit($commitPost);

                        }
                    }
                    $this->cache->set(sprintf('builderius_branch_%s_base_commit', $post->ID), $baseCommit);
                }
                return $baseCommit;
            },
            BuilderiusBranch::ACTIVE_COMMIT_NAME_FIELD => function () use ($post, $currUserId) {
                $commitsPosts = $this->cache->get(sprintf('builderius_branch_%s_commits_posts', $post->ID));
                if (false === $commitsPosts) {
                    $commitsPosts = $this->wpQuery->query([
                        'post_type' => BuilderiusCommitPostType::POST_TYPE,
                        'post_parent' => $post->ID,
                        'post_status' => get_post_stati(),
                        'posts_per_page' => 1,
                        'no_found_rows' => true,
                        'orderby' => 'ID',
                        'order' => 'DESC'
                    ]);
                }
                if (!empty($commitsPosts)) {
                    $activeCommitPost = reset($commitsPosts);
                    return $activeCommitPost->post_name;
                } else {
                    return null;
                }

                // logic for multiuser development
                /*$activeCommitName = null;
                $activeCommitNameString = get_post_meta($post->ID, BuilderiusBranch::ACTIVE_COMMIT_NAME_FIELD, true);
                $activeCommitNameJson = json_decode($activeCommitNameString, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if (isset($activeCommitNameJson[$currUserId])) {
                        $activeCommitName = $activeCommitNameJson[$currUserId];
                    } else {
                        $currUserHeadCommitPost = $this->cache->get(sprintf('builderius_branch_%d_head_commit_post_user_%d', $post->ID, $currUserId));
                        if (false === $currUserHeadCommitPost || null === $currUserHeadCommitPost) {
                            $currUserHeadCommitPost = $this->getBranchHeadCommitPost($post->ID, $currUserId);
                            $this->cache->set(sprintf('builderius_branch_%d_head_commit_post_user_%d', $post->ID, $currUserId), $currUserHeadCommitPost);
                            if (null === $currUserHeadCommitPost) {
                                $commitsPosts = $this->cache->get(sprintf('builderius_branch_%s_commits_posts', $post->ID));
                                if (false === $commitsPosts) {
                                    $commitsPosts = $this->wpQuery->query([
                                        'post_type' => BuilderiusCommitPostType::POST_TYPE,
                                        'post_parent' => $post->ID,
                                        'post_status' => get_post_stati(),
                                        'posts_per_page' => 1,
                                        'no_found_rows' => true,
                                        'orderby' => 'ID',
                                        'order' => 'DESC'
                                    ]);
                                }
                                if (!empty($commitsPosts)) {
                                    $activeCommitPost = reset($commitsPosts);
                                    $activeCommitName = $activeCommitPost->post_name;
                                }
                            }
                        }
                    }
                } elseif ($activeCommitNameString) {
                    $activeCommitName = $activeCommitNameString;
                } else {
                    $currUserHeadCommitPost = $this->getBranchHeadCommitPost($post->ID, $currUserId);
                    if (null === $currUserHeadCommitPost) {
                        $commitsPosts = $this->cache->get(sprintf('builderius_branch_%s_commits_posts', $post->ID));
                        if (false === $commitsPosts) {
                            $commitsPosts = $this->wpQuery->query([
                                'post_type' => BuilderiusCommitPostType::POST_TYPE,
                                'post_parent' => $post->ID,
                                'post_status' => get_post_stati(),
                                'posts_per_page' => 1,
                                'no_found_rows' => true,
                                'orderby' => 'ID',
                                'order' => 'DESC'
                            ]);
                        }
                        if (!empty($commitsPosts)) {
                            $activeCommitPost = reset($commitsPosts);
                            $activeCommitName = $activeCommitPost->post_name;
                        }
                    }
                }

                return $activeCommitName === "" ? null : $activeCommitName;*/
            },
            BuilderiusBranch::PUBLISHED_COMMIT_NAME_FIELD =>
                get_post_meta($post->ID, BuilderiusBranch::PUBLISHED_COMMIT_NAME_FIELD, true),
            BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD => function () use ($post, $currUserId) {
                $currUserNotCommittedConfig = $this->cache->get(sprintf('builderius_branch_%d_ncc_user_%d', $post->ID, $currUserId));
                if (false === $currUserNotCommittedConfig) {
                    $currUserHeadCommitPost = $this->cache->get(sprintf('builderius_branch_%d_head_commit_post_user_%d', $post->ID, $currUserId));
                    if (false === $currUserHeadCommitPost) {
                        $currUserHeadCommitPost = $this->getBranchHeadCommitPost($post->ID, $currUserId);
                        $this->cache->set(sprintf('builderius_branch_%d_head_commit_post_user_%d', $post->ID, $currUserId), $currUserHeadCommitPost);
                        if (null === $currUserHeadCommitPost) {
                            return null;
                        }
                    }
                    if (null === $currUserHeadCommitPost) {
                        return null;
                    }
                    $currUserNotCommittedConfig = json_decode(
                        get_post_meta(
                            $currUserHeadCommitPost->ID,
                            BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD,
                            true
                        ),
                        true
                    );
                    $this->cache->set(sprintf('builderius_branch_%d_ncc_user_%d', $post->ID, $currUserId), $currUserNotCommittedConfig);
                }

                return ContentConfigHelper::formatConfig($currUserNotCommittedConfig);
            },
            BuilderiusBranch::CONTENT_FIELD => function ($contentType = null) use ($post, $currUserId) {
                $currUserNotCommittedContent = $this->cache->get(sprintf('builderius_branch_%d_nccontent_user_%d', $post->ID, $currUserId));
                if (false === $currUserNotCommittedContent) {
                    $currUserHeadCommitPost = $this->cache->get(sprintf('builderius_branch_%d_head_commit_post_user_%d', $post->ID, $currUserId));
                    if (false === $currUserHeadCommitPost) {
                        $currUserHeadCommitPost = $this->getBranchHeadCommitPost($post->ID, $currUserId);
                        $this->cache->set(sprintf('builderius_branch_%d_head_commit_post_user_%d', $post->ID, $currUserId), $currUserHeadCommitPost);
                        if (null === $currUserHeadCommitPost) {
                            return null;
                        }
                    }
                    if (null === $currUserHeadCommitPost) {
                        return null;
                    }
                    $currUserNotCommittedContent = json_decode($currUserHeadCommitPost->post_content, true);
                    $this->cache->set(sprintf('builderius_branch_%d_nccontent_user_%d', $post->ID, $currUserId), $currUserNotCommittedContent);
                }
                if (null === $contentType) {
                    return $currUserNotCommittedContent;
                } elseif (is_array($currUserNotCommittedContent) && isset($currUserNotCommittedContent[$contentType])) {
                    return $currUserNotCommittedContent[$contentType];
                }
                return null;
            },
            BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD => function ()  use ($post, $currUserId) {
                $currUserNccBaseCommit = $this->cache->get(sprintf('builderius_branch_%d_ncc_base_commit_user_%d', $post->ID, $currUserId));
                if (false === $currUserNccBaseCommit) {
                    $currUserHeadCommitPost = $this->cache->get(sprintf('builderius_branch_%d_head_commit_post_user_%d', $post->ID, $currUserId));
                    if (false === $currUserHeadCommitPost) {
                        $currUserHeadCommitPost = $this->getBranchHeadCommitPost($post->ID, $currUserId);
                        $this->cache->set(sprintf('builderius_branch_%d_head_commit_post_user_%d', $post->ID, $currUserId), $currUserHeadCommitPost);
                        if (null === $currUserHeadCommitPost) {
                            return null;
                        }
                    }
                    if (null === $currUserHeadCommitPost) {
                        return null;
                    }
                    $currUserNccBaseCommit = get_post_meta(
                        $currUserHeadCommitPost->ID,
                        BuilderiusBranch::NCC_BASE_COMMIT_NAME_FIELD,
                        true
                    );
                    $this->cache->set(sprintf('builderius_branch_%d_ncc_base_commit_user_%d', $post->ID, $currUserId), $currUserNccBaseCommit);
                }

                return $currUserNccBaseCommit === "" ? null : $currUserNccBaseCommit;
            },
            BuilderiusBranch::OWNER_FIELD => function () use ($post) {
                $owner = $this->cache->get(sprintf('builderius_branch_%s_owner', $post->post_parent));
                if (false === $owner) {
                    /** @var \WP_Post $ownerPost */
                    $ownerPost = $this->cache->get(sprintf('builderius_branch_%s_owner_post', $post->post_parent));
                    if (false == $ownerPost) {
                        $ownerPost = get_post((int)$post->post_parent);
                        $this->cache->set(sprintf('builderius_branch_%s_owner_post', $post->post_parent), $ownerPost);
                    }
                    if ($ownerPost) {
                        $event = new BuilderiusVCSOwnerFromPostCreationEvent($ownerPost);
                        $this->eventDispatcher->dispatch($event, 'builderius_vcs_owner_from_post_creation');
                        $owner = $event->getOwner();
                        if ($owner instanceof BuilderiusVCSOwnerInterface) {
                            $this->cache->set(sprintf('builderius_branch_%s_owner', $post->post_parent), $owner);
                        }
                    }
                }

                return $owner;
            },
            BuilderiusBranch::INNER_COMMITS_TAGS_FIELD => function () use ($post) {
                global $wpdb;

                $sql = sprintf(
                    "SELECT DISTINCT t.name from %s t inner join %s tr on t.term_id = tr.term_taxonomy_id inner join %s tt on tr.term_taxonomy_id = tt.term_taxonomy_id join %s p on tr.object_id = p.ID where p.post_type = '%s' and tt.taxonomy = '%s' and p.post_parent = %d",
                    $wpdb->terms,
                    $wpdb->term_relationships,
                    $wpdb->term_taxonomy,
                    $wpdb->posts,
                    BuilderiusCommitPostType::POST_TYPE,
                    BuilderiusVCSTagTaxonomy::NAME,
                    $post->ID
                );

                $results = $wpdb->get_results($sql);
                $tags = [];
                foreach ($results as $result) {
                    $tags[] = $result->name;
                }

                return $tags;
            },
            BuilderiusBranch::COMMITS_FIELD => function () use ($post) {
                $commits = $this->cache->get(sprintf('builderius_branch_%s_commits', $post->ID));
                if (false === $commits) {
                    $commits = [];
                    $commitsPosts = $this->cache->get(sprintf('builderius_branch_%s_commits_posts', $post->ID));
                    if (false === $commitsPosts) {
                        $commitsPosts = $this->wpQuery->query([
                            'post_type' => BuilderiusCommitPostType::POST_TYPE,
                            'post_parent' => $post->ID,
                            'post_status' => get_post_stati(),
                            'posts_per_page' => -1,
                            'no_found_rows' => true,
                            'orderby' => 'ID',
                            'order' => 'DESC'
                        ]);
                        $this->cache->set(sprintf('builderius_branch_%s_commits_posts', $post->ID), $commitsPosts);
                    }
                    /** @var BuilderiusCommitInterface[] $commits */
                    foreach ($commitsPosts as $commitPost) {
                        if (false === $this->cache->get(sprintf('builderius_commit_post_%s', $commitPost->ID))) {
                            $this->cache->set(sprintf('builderius_commit_post_%s', $commitPost->ID), $commitPost);
                        }
                        $commit = $this->commitFromPostFactory->createCommit($commitPost);
                        $this->cache->set(sprintf('builderius_commit_%s', $commitPost->ID), $commit);
                        $commits[$commitPost->post_name] = $commit;
                    }
                    $this->cache->set(sprintf('builderius_branch_%s_commits', $post->ID), $commits);
                }
                return $commits;
            }
        ]);
    }

    /**
     * @param int $branchId
     * @param int $userId
     * @return \WP_Post|null
     */
    private function getBranchHeadCommitPost ($branchId, $userId)
    {
        $currUserHeadCommitsPosts = $this->wpQuery->query([
            'post_type' => BuilderiusBranchHeadCommitPostType::POST_TYPE,
            'post_parent' => $branchId,
            'name' => sprintf('branch_%d_user_%d', $branchId, $userId),
            'author' => $userId,
            'post_status' => get_post_stati(),
            'posts_per_page' => -1,
            'no_found_rows' => true,
            'orderby' => 'ID',
            'order' => 'DESC'
        ]);
        if (empty($currUserHeadCommitsPosts)) {
            return null;
        }
        return reset($currUserHeadCommitsPosts);
    }
}
