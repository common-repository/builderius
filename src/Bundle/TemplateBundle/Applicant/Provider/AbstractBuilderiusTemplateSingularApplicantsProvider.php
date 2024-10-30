<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\Provider;

use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicant;
use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantInterface;
use Builderius\Bundle\TemplateBundle\Applicant\Converter\PostToApplicantDataConverter;

abstract class AbstractBuilderiusTemplateSingularApplicantsProvider implements
    BuilderiusTemplateRuleApplicantsProviderInterface
{
    /**
     * @param \WP_Post $post
     * @param string|null $label
     * @param bool $withData
     * @param string|null $category
     * @param array $params
     * @param int $sortOrder
     * @return BuilderiusTemplateApplicantInterface
     */
    protected function convertPostToApplicant(
        \WP_Post $post,
        $label = null,
        $withData = false,
        $category = null,
        array $params = [],
        $sortOrder = 10
    )
    {
        if ($label === null) {
            $label = ucfirst($post->post_title);
        }
        return new BuilderiusTemplateApplicant([
            BuilderiusTemplateApplicant::LABEL_FIELD => $label,
            BuilderiusTemplateApplicant::URL_FIELD => get_permalink($post),
            BuilderiusTemplateApplicant::CATEGORY_FIELD => $category,
            BuilderiusTemplateApplicant::PARAMETERS_FIELD => $params,
            BuilderiusTemplateApplicant::DATA_FIELD => $withData ? PostToApplicantDataConverter::convert($post) : [],
            BuilderiusTemplateApplicant::SORT_ORDER_FIELD => $sortOrder
        ]);
    }
}