<?php

namespace Builderius\Bundle\SettingBundle\EventListener;

use Builderius\Bundle\SettingBundle\Registration\BuilderiusGlobalSettingsSetPostType;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;

class GlobalSettingsSetPostCreationOnNewTemplatePostCreationEventListener
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
            $technologyTerms = get_the_terms($templatePostId, BuilderiusTemplateTechnologyTaxonomy::NAME);
            if (!empty($technologyTerms)) {
                $technologyTerm = reset($technologyTerms);
                $technology = $technologyTerm->slug;
                $this->createGlobalSettingsSetPost($technology);
            }
        }
    }

    /**
     * @param $technology
     */
    private function createGlobalSettingsSetPost($technology)
    {
        $existingGlobalSettingsSetPosts = $this->wpQuery->query([
            'post_type' => BuilderiusGlobalSettingsSetPostType::POST_TYPE,
            'post_status' => get_post_stati(),
            'name' => $technology,
            'posts_per_page' => 1,
            'no_found_rows' => true,
        ]);
        if (empty($existingGlobalSettingsSetPosts)) {
            $currUserId = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
            $time = current_time('mysql');
            $globalSettingsSetArguments = [
                'post_name' => $technology,
                'post_type' => BuilderiusGlobalSettingsSetPostType::POST_TYPE,
                'post_author' => $currUserId,
                'post_date' => $time,
                'post_date_gmt' => get_gmt_from_date($time),
            ];
            $globalSettingsSetPostId = wp_insert_post(wp_slash($globalSettingsSetArguments), true);
            if (!is_wp_error($globalSettingsSetPostId)) {
                $branchArguments = [
                    'post_name' => 'master',
                    'post_parent' => $globalSettingsSetPostId,
                    'post_type' => BuilderiusBranchPostType::POST_TYPE,
                    'post_author' => $currUserId,
                    'post_date' => $time,
                    'post_date_gmt' => get_gmt_from_date($time),
                ];
                wp_insert_post(wp_slash($branchArguments), true);
            }
        }
    }
}