<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\TemplateBundle\DynamicDataHelper\DynamicDataHelper;
use Builderius\Bundle\TemplateBundle\DynamicDataHelper\DynamicDataHelperArgument;
use Builderius\Bundle\TemplateBundle\DynamicDataHelper\DynamicDataHelpersCategoriesProviderInterface;
use Builderius\Bundle\TemplateBundle\DynamicDataHelper\DynamicDataHelpersCategory;
use Builderius\Bundle\TemplateBundle\DynamicDataHelper\DynamicDataHelpersProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;

class BuilderiusDynamicDataHelpersScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'dynamicDataHelpers';

    /**
     * @var DynamicDataHelpersProviderInterface
     */
    private $dynamicDataHelpersProvider;

    /**
     * @param DynamicDataHelpersProviderInterface $dynamicDataHelpersProvider
     */
    public function __construct(
        DynamicDataHelpersProviderInterface $dynamicDataHelpersProvider
    ) {
        $this->dynamicDataHelpersProvider = $dynamicDataHelpersProvider;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $data = [];
        foreach ($this->dynamicDataHelpersProvider->getDynamicDataHelpers() as $helper) {
            $data[$helper->getCategory()][$helper->getName()] = [
                DynamicDataHelper::LABEL_FIELD => __($helper->getLabel()),
                DynamicDataHelper::SORT_ORDER_FIELD => $helper->getSortOrder(),
                DynamicDataHelper::GRAPHQL_PATH_FIELD => $helper->getGraphQLPath(),
                DynamicDataHelper::EXPRESSION_FIELD => $helper->getExpression(),
                DynamicDataHelper::ESCAPED_FIELD => $helper->isEscaped(),
                DynamicDataHelper::TYPE_FIELD => $helper->getType(),
            ];
            if (!empty($helper->getArguments())) {
                $args = [];
                foreach ($helper->getArguments() as $argument) {
                    $args[$argument->getName()] = [
                        DynamicDataHelperArgument::TYPE_FIELD => $argument->getType(),
                        DynamicDataHelperArgument::VALUE_LIST_FIELD => $argument->getValueList(),
                        DynamicDataHelperArgument::VALUE_FIELD => $argument->getValue(),
                        DynamicDataHelperArgument::PLACEHOLDER_FIELD => $argument->getPlaceholder(),
                        DynamicDataHelperArgument::ENUM_FIELD => $argument->isEnum(),
                    ];
                }
                $data[$helper->getCategory()][$helper->getName()][DynamicDataHelper::ARGUMENTS_FIELD] = $args;
            }
        }

        return $data;
    }
}
