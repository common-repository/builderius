<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Event\ObjectContainingEvent;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;

class BranchesCountCheckingEventListener
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @param \WP_Query $wpQuery
     */
    public function __construct(\WP_Query $wpQuery)
    {
        $this->wpQuery = $wpQuery;
    }

    public function beforeBranchCreate(ObjectContainingEvent $event)
    {
        $branchPost = $event->getObject();
        if ($branchPost) {
            $existingBranchPosts = $this->wpQuery->query([
                'post_type' => BuilderiusBranchPostType::POST_TYPE,
                'post_parent' => $branchPost->post_parent,
                'posts_per_page' => -1,
                'no_found_rows' => true,
                'post_status' => get_post_stati()
            ]);
            if (count($existingBranchPosts) > 2) {
                $error = new \WP_Error(
                    'max_branches_count',
                    __('Free version has a limit of 2 branches'),
                    ['status' => 400]
                );
                $event->setError($error);
                $event->stopPropagation();
            }
        }
    }
}