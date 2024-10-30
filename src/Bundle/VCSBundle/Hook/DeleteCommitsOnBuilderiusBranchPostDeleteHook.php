<?php

namespace Builderius\Bundle\VCSBundle\Hook;

use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusCommitPostType;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class DeleteCommitsOnBuilderiusBranchPostDeleteHook extends AbstractAction
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

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
     * @inheritDoc
     */
    public function getFunction()
    {
        $postId = func_get_arg(0);
        $branchPost = $this->wpQuery->query([
            'post_type' => BuilderiusBranchPostType::POST_TYPE,
            'p' => $postId,
            'post_status' => get_post_stati()
        ]);
        if ($branchPost) {
            $builderiusCommitPosts = $this->wpQuery->query([
                'post_type' => BuilderiusCommitPostType::POST_TYPE,
                'post_parent' => $postId,
                'post_status' => get_post_stati()
            ]);
            if (!empty($builderiusCommitPosts)) {
                $ids = [];
                foreach ($builderiusCommitPosts as $builderiusCommitPost) {
                    $ids[] = $builderiusCommitPost->ID;
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
