<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicantCategoriesProviderInterface;
use Builderius\Bundle\TemplateBundle\DynamicDataHelper\DynamicDataHelpersCategoriesProviderInterface;
use Builderius\Bundle\TemplateBundle\DynamicDataHelper\DynamicDataHelpersCategory;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;

class BuilderiusTemplateApplicantsCategoriesScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'applicantsCategories';

    /**
     * @var BuilderiusTemplateApplicantCategoriesProviderInterface
     */
    private $applicantsCategoriesProvider;

    /**
     * @param BuilderiusTemplateApplicantCategoriesProviderInterface $applicantsCategoriesProvider
     */
    public function __construct(
        BuilderiusTemplateApplicantCategoriesProviderInterface $applicantsCategoriesProvider
    ) {
        $this->applicantsCategoriesProvider = $applicantsCategoriesProvider;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $data = [];
        foreach ($this->applicantsCategoriesProvider->getCategories() as $category) {
            $data[$category->getName()] = [
                DynamicDataHelpersCategory::LABEL_FIELD => __($category->getLabel()),
                DynamicDataHelpersCategory::SORT_ORDER_FIELD => $category->getSortOrder()
            ];
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return 20;
    }
}
