<?php

namespace Builderius\Bundle\DeliverableBundle\EventListener;

use Builderius\Bundle\DeliverableBundle\Registration\BulderiusDeliverableSubModulePostType;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;

class BuilderiusDeliverableDeletedEventListener
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
    public function onDeliverableDeleted(PostContainingEvent $event)
    {
        $subModulesPosts = $this->wpQuery->query([
            'post_type' => BulderiusDeliverableSubModulePostType::POST_TYPE,
            'post_parent' => $event->getPost()->ID,
            'post_status' => get_post_stati(),
            'posts_per_page' => -1,
            'no_found_rows' => true,
            'orderby' => 'ID',
            'order' => 'DESC'
        ]);
        foreach ($subModulesPosts as $subModulePost) {
            $deletedPost = wp_delete_post($subModulePost->ID, true);
            if ($deletedPost instanceof \WP_Error) {
                throw new \Exception($deletedPost->get_error_message(), 400);
            }
        }
    }
}