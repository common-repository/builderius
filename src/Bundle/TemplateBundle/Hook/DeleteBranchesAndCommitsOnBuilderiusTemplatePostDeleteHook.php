<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\VCSBundle\Factory\BuilderiusBranchFromPostFactory;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class DeleteBranchesAndCommitsOnBuilderiusTemplatePostDeleteHook extends AbstractAction
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusBranchFromPostFactory
     */
    private $branchFactory;

    /**
     * @param \WP_Query $wpQuery
     * @return $this
     */
    public function setWpQuery(\WP_Query $wpQuery)
    {
        $this->wpQuery = $wpQuery;

        return $this;
    }

    /**
     * @param BuilderiusBranchFromPostFactory $branchFactory
     * @return $this
     */
    public function setBranchFactory(BuilderiusBranchFromPostFactory $branchFactory)
    {
        $this->branchFactory = $branchFactory;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $postId = func_get_arg(0);
        $templatePost = $this->wpQuery->query([
            'post_type' => BuilderiusTemplatePostType::POST_TYPE,
            'p' => $postId,
            'post_status' => get_post_stati()
        ]);
        if ($templatePost) {
            $builderiusBranchPosts = $this->wpQuery->query([
                'post_type' => BuilderiusBranchPostType::POST_TYPE,
                'post_parent' => $postId,
                'post_status' => get_post_stati()
            ]);
            if (!empty($builderiusBranchPosts)) {
                $ids = [];
                foreach ($builderiusBranchPosts as $builderiusBranchPost) {
                    $builderiusBranch = $this->branchFactory->createBranch($builderiusBranchPost);
                    $ids[] = $builderiusBranch->getId();
                    foreach ($builderiusBranch->getCommits() as $builderiusTemplateCommit) {
                        $ids[] = $builderiusTemplateCommit->getId();
                    }
                }
                if (!empty($ids)) {
                    global $wpdb;
                    $deletePostsQuery = $wpdb->prepare(
                        'DELETE FROM %1$s WHERE %2$s.ID IN (%3$s)',
                        $wpdb->posts,
                        $wpdb->posts,
                        join(',', $ids)
                    );
                    $deletePostsMetaQuery = $wpdb->prepare(
                        'DELETE FROM %1$s WHERE %2$s.post_id IN (%3$s)',
                        $wpdb->postmeta,
                        $wpdb->postmeta,
                        join(',', $ids)
                    );
                    $deletePostsTermsQuery = $wpdb->prepare(
                        'DELETE FROM %1$s WHERE %2$s.object_id IN (%3$s)',
                        $wpdb->term_relationships,
                        $wpdb->term_relationships,
                        join(',', $ids)
                    );
                    $wpdb->query($deletePostsQuery);
                    $wpdb->query($deletePostsMetaQuery);
                    $wpdb->query($deletePostsTermsQuery);
                }
            }
        }
    }
}
