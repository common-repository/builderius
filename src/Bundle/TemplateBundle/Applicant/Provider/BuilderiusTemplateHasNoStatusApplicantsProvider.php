<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\Provider;

use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantCategoriesProvider;
use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantCategoriesProviderInterface;
use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantCategory;
use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantChangeSetInterface;

class BuilderiusTemplateHasNoStatusApplicantsProvider extends AbstractBuilderiusTemplateSingularApplicantsProvider
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusTemplateApplicantCategoriesProviderInterface|BuilderiusTemplateApplicantCategoriesProvider
     */
    private $applicantCategoriesProvider;

    /**
     * @param \WP_Query $wpQuery
     * @param BuilderiusTemplateApplicantCategoriesProviderInterface $applicantCategoriesProvider
     */
    public function __construct(
        \WP_Query $wpQuery,
        BuilderiusTemplateApplicantCategoriesProviderInterface $applicantCategoriesProvider
    ) {
        $this->wpQuery = $wpQuery;
        $this->applicantCategoriesProvider = $applicantCategoriesProvider;
    }

    /**
     * @inheritDoc
     */
    public function getApplicants($rule, $argument, $operator, $withData = false)
    {
        $postType = $this->getRulePostType($rule);
        if ($postType === 'post') {
            $labelPrefix = __('Blog post');
            $categoryName = 'blog_posts';
        } elseif ($postType === 'page') {
            $labelPrefix = __('Static page');
            $categoryName = 'static_pages';
        } else {
            /** @var \WP_Post_Type $postTypeObject */
            $postTypeObject = get_post_type_object($postType);
            $categoryName = $postType . '_posts';
            $labelPrefix = __($postTypeObject->labels->singular_name);
            if (!$this->applicantCategoriesProvider->hasCategory($categoryName)) {
                $category = new BuilderiusTemplateApplicantCategory([
                    BuilderiusTemplateApplicantCategory::NAME_FIELD => $categoryName,
                    BuilderiusTemplateApplicantCategory::LABEL_FIELD => $postTypeObject->labels->name,
                    BuilderiusTemplateApplicantCategory::SORT_ORDER_FIELD => 40
                ]);
                $this->applicantCategoriesProvider->addCategory($category);
            }
        }
        $statuses = get_post_stati();
        unset($statuses[$argument]);
        unset($statuses['auto-draft']);

        $posts = $this->wpQuery->query([
            'lang' => '',
            'post_type' => $postType,
            'post_status' => $statuses,
            'posts_per_page' => -1,
            'no_found_rows' => true,
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
                        $this->convertPostToApplicant($post, sprintf('%s: %s', $labelPrefix, $post->post_title), $withData, $categoryName);
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
        $postType = $this->getRulePostType($rule);
        if ($postType === 'post') {
            $labelPrefix = __('Blog post');
            $categoryName = 'blog_posts';
        } elseif ($postType === 'page') {
            $labelPrefix = __('Static page');
            $categoryName = 'static_pages';
        } else {
            /** @var \WP_Post_Type $postTypeObject */
            $postTypeObject = get_post_type_object($postType);
            $categoryName = $postType . '_posts';
            $labelPrefix = __($postTypeObject->labels->singular_name);
            if (!$this->applicantCategoriesProvider->hasCategory($categoryName)) {
                $category = new BuilderiusTemplateApplicantCategory([
                    BuilderiusTemplateApplicantCategory::NAME_FIELD => $categoryName,
                    BuilderiusTemplateApplicantCategory::LABEL_FIELD => $postTypeObject->labels->name,
                    BuilderiusTemplateApplicantCategory::SORT_ORDER_FIELD => 40
                ]);
                $this->applicantCategoriesProvider->addCategory($category);
            }
        }
        $frontpageId = get_option('page_on_front');
        $postsPageId = get_option('page_for_posts');
        $showOnFront = get_option('show_on_front');

        $object = $changeset->getAction() === BuilderiusTemplateApplicantChangeSetInterface::DELETE_ACTION ?
            $changeset->getObjectBefore() : $changeset->getObjectAfter();
        if (!$object instanceof \WP_Post || $object->post_type !== $postType || $object->post_status === $argument) {
            return [];
        }
        if ($showOnFront !== 'posts' && $postsPageId && $object->ID === (int)$postsPageId) {
            return [];
        } else {
            if ($frontpageId && $showOnFront === 'page' && (int)$object->ID == (int)$frontpageId) {
                return [sprintf('singular.single.%s.%s', $object->post_type, $object->ID) =>
                    $this->convertPostToApplicant($object, sprintf('%s: %s', __('Front page'), $object->post_title), $withData, 'special_pages')
                ];
            } else {
                return [sprintf('singular.single.%s.%s', $object->post_type, $object->ID) =>
                    $this->convertPostToApplicant($object, sprintf('%s: %s', $labelPrefix, $object->post_title), $withData, $categoryName)
                ];
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function isApplicable($rule, $argument, $operator)
    {
        if (strpos($rule, 'has_status') !== false && is_string($argument) && $operator === '!=') {
            return true;
        }

        return false;
    }

    /**
     * @param string $rule
     * @return string
     */
    private function getRulePostType($rule)
    {
        $postType = str_replace(
            'singular.',
            '',
            str_replace(
                '.has_status',
                '',
                str_replace(
                    'single.',
                    '',
                    str_replace(
                        'custom_post.',
                        '',
                        $rule
                    )
                )
            )
        );
        if ($postType === 'blog_post') {
            $postType = 'post';
        }

        return $postType;
    }
}