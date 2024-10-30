<?php

namespace Builderius\Bundle\VCSBundle\Factory;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Helper\ContentConfigHelper;
use Builderius\Bundle\VCSBundle\Event\BuilderiusBranchFromPostCreationEvent;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranch;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranchInterface;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommit;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommitInterface;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusVCSTagTaxonomy;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;

class BuilderiusCommitFromPostFactory
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

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
     * @param BuilderiusRuntimeObjectCache $cache
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(\WP_Query $wpQuery, BuilderiusRuntimeObjectCache $cache, EventDispatcher $eventDispatcher) {
        $this->wpQuery = $wpQuery;
        $this->cache = $cache;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \WP_Post $post
     * @return BuilderiusCommitInterface
     */
    public function createCommit(\WP_Post $post)
    {
        $mergedBranchName = get_post_meta($post->ID, BuilderiusCommit::MERGED_BRANCH_NAME_FIELD, true) ? : null;
        $mergedCommitName = get_post_meta($post->ID, BuilderiusCommit::MERGED_COMMIT_NAME_FIELD, true) ? : null;
        $tagTerms = get_the_terms($post->ID, BuilderiusVCSTagTaxonomy::NAME);
        $vcsTags = [];
        if (is_array($tagTerms)) {
            foreach ($tagTerms as $tagTerm) {
                $vcsTags[] = $tagTerm->name;
            }
        }
        $commit = new BuilderiusCommit([
            BuilderiusCommit::ID_FIELD => $post->ID,
            BuilderiusCommit::BRANCH_ID_FIELD => $post->post_parent,
            BuilderiusCommit::NAME_FIELD => $post->post_name,
            BuilderiusCommit::AUTHOR_FIELD => get_user_by('ID', $post->post_author),
            BuilderiusCommit::DESCRIPTION_FIELD => $post->post_excerpt,
            BuilderiusCommit::MERGED_BRANCH_NAME_FIELD => $mergedBranchName,
            BuilderiusCommit::MERGED_BRANCH_FIELD => function () use ($post, $mergedBranchName) {
                if (!$mergedBranchName) {
                    return null;
                }
                $mergedBranch = $this->cache->get(sprintf('builderius_commit_%s_merged_branch', $post->ID));
                if (false !== $mergedBranch) {
                    return $mergedBranch;
                }
                /** @var BuilderiusBranch $branch */
                $branch = $this->cache->get(sprintf('builderius_branch_%s', $post->post_parent));
                if (false == $branch) {
                    $branchPost = $this->cache->get(sprintf('builderius_branch_post_%s', $post->post_parent));
                    if (false === $branchPost) {
                        $branchPost = get_post((int)$post->post_parent);
                        $this->cache->set(sprintf('builderius_branch_post_%s', $post->post_parent), $branchPost);
                    }
                    $ownerId = $branchPost->post_parent;
                } else {
                    $ownerId = $branch->getOwnerId();
                }
                $mergedBranchPosts = $this->wpQuery->query([
                    'name' => $mergedBranchName,
                    'post_type' => [BuilderiusBranchPostType::POST_TYPE],
                    'post_parent' => $ownerId,
                    'post_status' => get_post_stati(),
                    'posts_per_page' => 1,
                    'no_found_rows' => true,
                ]);
                if (!empty($mergedBranchPosts)) {
                    $mergedBranchPost = reset($mergedBranchPosts);
                    $event = new BuilderiusBranchFromPostCreationEvent($mergedBranchPost);
                    $this->eventDispatcher->dispatch($event, 'builderius_branch_from_post_creation');
                    $mergedBranch = $event->getBranch();
                    if ($mergedBranch instanceof BuilderiusBranchInterface) {
                        $this->cache->set(sprintf('builderius_branch_%s', $mergedBranch->getId()), $mergedBranch);
                        $this->cache->set(sprintf('builderius_commit_%s_merged_branch', $post->ID), $mergedBranch);
                        return $mergedBranch;
                    }
                }
                return null;
            },
            BuilderiusCommit::MERGED_COMMIT_NAME_FIELD => $mergedCommitName,
            BuilderiusCommit::MERGED_COMMIT_FIELD => function () use ($post, $mergedCommitName) {
                if (!$mergedCommitName) {
                    return null;
                }
                $mergedCommit = $this->cache->get(sprintf('builderius_commit_%s_merged_commit', $post->ID));
                if (false !== $mergedCommit) {
                    return $mergedCommit;
                }
                $mergedCommitPosts = $this->wpQuery->query([
                    'name' => $mergedCommitName,
                    'post_type' => [BuilderiusCommitPostType::POST_TYPE],
                    'post_status' => get_post_stati(),
                    'posts_per_page' => 1,
                    'no_found_rows' => true,
                ]);
                if (!empty($mergedCommitPosts)) {
                    $mergedCommitPost = reset($mergedCommitPosts);
                    $mergedCommit = $this->createCommit($mergedCommitPost);
                    if ($mergedCommit instanceof BuilderiusCommitInterface) {
                        $this->cache->set(sprintf('builderius_commit_%s', $mergedCommit->getId()), $mergedCommit);
                        $this->cache->set(sprintf('builderius_commit_%s_merged_commit', $post->ID), $mergedCommit);
                        return $mergedCommit;
                    }
                }
                return null;
            },
            BuilderiusCommit::TAGS_FIELD => $vcsTags,
            BuilderiusCommit::CREATED_AT_FIELD => $post->post_date,
            BuilderiusCommit::BRANCH_FIELD => function () use ($post) {
                $branch = $this->cache->get(sprintf('builderius_branch_%s', $post->post_parent));
                if (false === $branch) {
                    $branch = null;
                    $branchPost = $this->cache->get(sprintf('builderius_branch_post_%s', $post->post_parent));
                    if (false === $branchPost) {
                        $branchPost = get_post((int)$post->post_parent);
                        $this->cache->set(sprintf('builderius_branch_post_%s', $post->post_parent), $branchPost);
                    }
                    if ($branchPost) {
                        $event = new BuilderiusBranchFromPostCreationEvent($branchPost);
                        $this->eventDispatcher->dispatch($event, 'builderius_branch_from_post_creation');
                        $branch = $event->getBranch();
                        if ($branch instanceof BuilderiusBranchInterface) {
                            $this->cache->set(sprintf('builderius_branch_%s', $post->post_parent), $branch);
                        }
                    }
                }
                return $branch;
            },
            BuilderiusCommit::CONTENT_CONFIG_FIELD =>
                ContentConfigHelper::formatConfig(
                    json_decode(
                        get_post_meta(
                            $post->ID,
                            BuilderiusCommit::CONTENT_CONFIG_FIELD,
                            true
                        ),
                        true
                    )
                ),
        ]);
        $commit->setContent(json_decode($post->post_content, true));

        return $commit;
    }
}
