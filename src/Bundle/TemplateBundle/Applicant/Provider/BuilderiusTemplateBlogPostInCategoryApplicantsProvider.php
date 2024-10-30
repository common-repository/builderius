<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\Provider;

use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantChangeSetInterface;

class BuilderiusTemplateBlogPostInCategoryApplicantsProvider extends AbstractBuilderiusTemplateSingularApplicantsProvider
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
            'post_type' => ['post'],
            'post_status' => $statuses,
            'posts_per_page' => -1,
            'no_found_rows' => true,
            'tax_query' => [
                [
                    'taxonomy' => 'category',
                    'field'    => 'id',
                    'terms'    => $argument,
                ],
            ],
        ]);
        $applicants = [];
        foreach ($posts as $post) {
            $applicants[sprintf('singular.single.%s.%s', $post->post_type, $post->ID)] =
                $this->convertPostToApplicant($post, sprintf('%s: %s', __('Blog post'), $post->post_title), $withData, 'blog_posts');
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
        if (!$object instanceof \WP_Post || $object->post_type !== 'post' || !has_category($argument, $object)) {
            return [];
        }

        return [
            sprintf('singular.single.%s.%s', $object->post_type, $object->ID) =>
                $this->convertPostToApplicant($object, sprintf('%s: %s', __('Blog post'), $object->post_title), $withData, 'blog_posts')
        ];
    }

    /**
     * @inheritDoc
     */
    public function isApplicable($rule, $argument, $operator)
    {
        if (strpos($rule, 'singular.single.blog_post.in_category') !== false && is_integer($argument) && $operator === '==') {
            return true;
        }

        return false;
    }
}