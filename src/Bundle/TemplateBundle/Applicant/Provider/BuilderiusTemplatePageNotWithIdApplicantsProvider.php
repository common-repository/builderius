<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\Provider;

use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantChangeSetInterface;

class BuilderiusTemplatePageNotWithIdApplicantsProvider extends AbstractBuilderiusTemplateSingularApplicantsProvider
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
     * @inheritDoc
     */
    public function getApplicants($rule, $argument, $operator, $withData = false)
    {
        $statuses = get_post_stati();
        unset($statuses['auto-draft']);

        $posts = $this->wpQuery->query([
            'lang' => '',
            'posts_per_page' => -1,
            'no_found_rows' => true,
            'post_type' => ['page'],
            'post__not_in' => [$argument],
            'post_status' => $statuses,
        ]);
        $applicants = [];
        $frontpageId = get_option('page_on_front');
        $postsPageId = get_option('page_for_posts');
        $showOnFront = get_option('show_on_front');

        foreach ($posts as $post) {
            if ($showOnFront !== 'posts' && $postsPageId && $post->ID === (int)$postsPageId) {
                continue;
            } else {
                if ($frontpageId && $showOnFront === 'page' && (int)$post->ID == (int)$frontpageId) {
                    $applicants[sprintf('singular.single.%s.%s', $post->post_type, $post->ID)] =
                        $this->convertPostToApplicant($post, sprintf('%s: %s', __('Front page'), $post->post_title), $withData, 'special_pages');
                } else {
                    $applicants[sprintf('singular.single.%s.%s', $post->post_type, $post->ID)] =
                        $this->convertPostToApplicant($post, sprintf('%s: %s', __('Static page'), $post->post_title), $withData, 'static_pages');
                }
            }
        }

        return $applicants;
    }

    /**
     * @inheritDoc
     */
    public function getChangeSetApplicants(
        BuilderiusTemplateApplicantChangeSetInterface $changeset,
        $rule,
        $argument,
        $operator,
        $withData = false
    ) {
        $object = $changeset->getAction() === BuilderiusTemplateApplicantChangeSetInterface::DELETE_ACTION ?
            $changeset->getObjectBefore() : $changeset->getObjectAfter();
        if (!$object instanceof \WP_Post || $object->post_type !== 'page' || $object->ID === $argument) {
            return [];
        }
        $frontpageId = get_option('page_on_front');
        $postsPageId = get_option('page_for_posts');
        $showOnFront = get_option('show_on_front');

        if ($showOnFront !== 'posts' && $postsPageId && $object->ID === (int)$postsPageId) {
            return [];
        } else {
            if ($frontpageId && $showOnFront === 'page' && (int)$object->ID == (int)$frontpageId) {
                return [
                    sprintf('singular.single.%s.%s', $object->post_type, $object->ID) =>
                        $this->convertPostToApplicant($object, sprintf('%s: %s', __('Front page'), $object->post_title), $withData, 'special_pages')
                ];
            } else {
                return [
                    sprintf('singular.single.%s.%s', $object->post_type, $object->ID) =>
                        $this->convertPostToApplicant($object, sprintf('%s: %s', __('Static page'), $object->post_title), $withData, 'static_pages')
                ];
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function isApplicable($rule, $argument, $operator)
    {
        if (strpos($rule, 'singular.page.with_id') !== false && is_integer($argument) && $operator === '!=') {
            return true;
        }

        return false;
    }
}