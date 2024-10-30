<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;

class BuilderiusTemplatesDeleteBeforeDeliverableExtractListener
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

    /**
     * @param PostContainingEvent $event
     */
    public function beforeDeliverableExtraction(PostContainingEvent $event)
    {
        $queryArgs = [
            'post_type' => BuilderiusTemplatePostType::POST_TYPE,
            'post_status' => get_post_stati(),
            'posts_per_page' => -1,
            'no_found_rows' => true,
        ];

        $posts = $this->wpQuery->query($queryArgs);
        foreach ($posts as $post) {
            wp_delete_post($post->ID);
        }
    }
}