<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicant;
use Builderius\Bundle\TemplateBundle\ApplyRule\Category\BuilderiusTemplateApplicantParametersAwareApplyRuleCategoryInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Category\BuilderiusTemplateApplicantsAwareApplyRuleCategoryInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Category\Registry\BuilderiusTemplateApplyRuleCategoriesRegistryInterface;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Provider\Applicant\BuilderiusTemplateApplicantsProviderInterface;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;

class BuilderiusTemplatePossibleApplicantsScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'applicants';

    /**
     * @var BuilderiusTemplateFromPostFactory
     */
    private $builderiusTemplateFromPostFactory;

    /**
     * @var BuilderiusTemplateApplicantsProviderInterface
     */
    private $applicantsProvider;

    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusTemplateApplyRuleCategoriesRegistryInterface
     */
    private $applyRuleCategoriesRegistry;

    /**
     * @var BuilderiusTemplateProviderInterface
     */
    private $builderiusTemplateProvider;

    /**
     * @param BuilderiusTemplateFromPostFactory $builderiusTemplateFromPostFactory
     * @param BuilderiusTemplateApplicantsProviderInterface $applicantsProvider
     * @param \WP_Query $wpQuery
     * @param BuilderiusTemplateApplyRuleCategoriesRegistryInterface $applyRuleCategoriesRegistry
     * @param BuilderiusTemplateProviderInterface $builderiusTemplateProvider
     */
    public function __construct(
        BuilderiusTemplateFromPostFactory $builderiusTemplateFromPostFactory,
        BuilderiusTemplateApplicantsProviderInterface $applicantsProvider,
        \WP_Query $wpQuery,
        BuilderiusTemplateApplyRuleCategoriesRegistryInterface $applyRuleCategoriesRegistry,
        BuilderiusTemplateProviderInterface $builderiusTemplateProvider
    ) {
        $this->builderiusTemplateFromPostFactory = $builderiusTemplateFromPostFactory;
        $this->applicantsProvider = $applicantsProvider;
        $this->wpQuery = $wpQuery;
        $this->applyRuleCategoriesRegistry = $applyRuleCategoriesRegistry;
        $this->builderiusTemplateProvider = $builderiusTemplateProvider;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $template = $this->builderiusTemplateProvider->getTemplate();
        if ($template) {
            $id = $template->getId();
            $type = $template->getType();
            $technology = $template->getTechnology();
            $sortOrder = $template->getSortOrder();

            $applicants = $this->applicantsProvider->getApplicants($template->getApplyRulesConfig());
            if (!empty($applicants)) {
                foreach($applicants as $applicant) {
                    $applicant->setData($this->getAdditionalCategoriesParameters($template->getApplyRulesConfig()));
                }
                $query = [
                    'post_type' => BuilderiusTemplatePostType::POST_TYPE,
                    'post_status' => ['publish', 'future'],
                    'post__not_in' => [$id],
                    'posts_per_page' => -1,
                    'no_found_rows' => true,
                    'tax_query' => [
                        'relation' => 'AND',
                        [
                            'taxonomy' => BuilderiusTemplateTypeTaxonomy::NAME,
                            'field' => 'slug',
                            'terms' => [$type],
                            'operator' => 'IN'
                        ],
                        [
                            'taxonomy' => BuilderiusTemplateSubTypeTaxonomy::NAME,
                            'field' => 'slug',
                            'terms' => ['regular'],
                            'operator' => 'IN'
                        ],
                        [
                            'taxonomy' => BuilderiusTemplateTechnologyTaxonomy::NAME,
                            'field' => 'slug',
                            'terms' => [$technology],
                            'operator' => 'IN'
                        ]
                    ]
                ];
                if ($template->getSubType() === 'regular') {
                    $query['meta_key'] = BuilderiusTemplate::SORT_ORDER_FIELD;
                    $query['meta_value_num'] = $sortOrder;
                    $query['meta_compare'] = '<=';
                }
                $templatePostsWithHigherPriority = $this->wpQuery->query($query);
                $excludeApplicants = [];
                if (!empty($templatePostsWithHigherPriority)) {
                    foreach ($templatePostsWithHigherPriority as $templatePostWithHigherPriority) {
                        $templateWithHigherPriority = $this->builderiusTemplateFromPostFactory->createTemplate($templatePostWithHigherPriority);
                        if (
                            (
                                $template->getSubType() === 'regular' &&
                                (
                                    $templateWithHigherPriority->getSortOrder() < $sortOrder ||
                                    (
                                        $templateWithHigherPriority->getSortOrder() == $sortOrder &&
                                        $templateWithHigherPriority->getId() < $template->getId()
                                    )
                                )
                            ) || $template->getSubType() === 'hook'
                        ) {

                            try {
                                $iterationExclApplicants = $this->applicantsProvider->getApplicants($templateWithHigherPriority->getApplyRulesConfig());
                                foreach($iterationExclApplicants as $iterationExclApplicant) {
                                    $iterationExclApplicant->setData($this->getAdditionalCategoriesParameters($templateWithHigherPriority->getApplyRulesConfig()));
                                }
                                $excludeApplicants = array_merge(
                                    $excludeApplicants,
                                    $iterationExclApplicants
                                );
                            } catch (\Exception $e) {
                                continue;
                            }
                        }
                    }
                }

                $excludeApplicantsArr = [];
                foreach ($excludeApplicants as $excludeApplicant) {
                    $excludeApplicantsArr[] = [
                        BuilderiusTemplateApplicant::URL_FIELD => $excludeApplicant->getUrl(),
                        BuilderiusTemplateApplicant::PARAMETERS_FIELD => $excludeApplicant->getParameters(),
                    ];
                }
                $data = [];
                foreach ($applicants as $k => $applicant) {
                    $applicantArr = [
                        BuilderiusTemplateApplicant::URL_FIELD => $applicant->getUrl(),
                        BuilderiusTemplateApplicant::PARAMETERS_FIELD => $applicant->getParameters(),
                    ];
                    if (!in_array($applicantArr, $excludeApplicantsArr)) {
                        $data[$applicant->getCategory() ?: 'other'][$k] = [
                            BuilderiusTemplateApplicant::LABEL_FIELD => __($applicant->getLabel()),
                            BuilderiusTemplateApplicant::URL_FIELD => $applicant->getUrl(),
                            BuilderiusTemplateApplicant::PARAMETERS_FIELD => $applicant->getParameters(),
                            BuilderiusTemplateApplicant::CATEGORY_FIELD => __($applicant->getCategory()),
                            BuilderiusTemplateApplicant::SORT_ORDER_FIELD => $applicant->getSortOrder()
                        ];
                    }
                }

                return $data;
            }
        }

        return [];
    }

    /**
     * @param array $applyRulesConfig
     * @return array
     */
    private function getAdditionalCategoriesParameters(array $applyRulesConfig)
    {
        $additionalCategoriesParams = [];
        foreach ($applyRulesConfig['categories'] as $categoryName => $configSet) {
            $category = $this->applyRuleCategoriesRegistry->getCategory($categoryName);
            if (
                $category &&
                !$category instanceof BuilderiusTemplateApplicantsAwareApplyRuleCategoryInterface &&
                !$category instanceof BuilderiusTemplateApplicantParametersAwareApplyRuleCategoryInterface
            ) {
                $additionalCategoriesParams[$categoryName] = $configSet;
            }
        }

        return $additionalCategoriesParams;
    }
}