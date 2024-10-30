<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;

class MasterBranchForNewTemplatePostCreationEventListener
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @param \WP_Query $wpQuery
     */
    public function __construct(
        \WP_Query $wpQuery
    ) {
        $this->wpQuery = $wpQuery;
    }


    /**
     * @param PostContainingEvent $event
     */
    public function onTemplateCreation(PostContainingEvent $event)
    {
        $templatePost = $event->getPost();
        if ($templatePost && $templatePost->post_type === BuilderiusTemplatePostType::POST_TYPE) {
            $templatePostId = $templatePost->ID;
            $builderiusBranchPosts = $this->wpQuery->query([
                'post_type' => BuilderiusBranchPostType::POST_TYPE,
                'post_parent' => $templatePostId,
                'post_status' => get_post_stati(),
                'posts_per_page' => 1,
                'name' => 'master',
            ]);
            if (empty($builderiusBranchPosts)) {
                $time = current_time('mysql');
                $branchArguments = [
                    'post_name' => 'master',
                    'post_parent' => $templatePostId,
                    'post_type' => BuilderiusBranchPostType::POST_TYPE,
                    'post_author' => apply_filters('builderius_get_current_user', wp_get_current_user())->ID,
                    'post_date' => $time,
                    'post_date_gmt' => get_gmt_from_date($time),
                ];
                wp_insert_post(wp_slash($branchArguments), true);
            }
        }
    }
}