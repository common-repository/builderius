<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateSubTypeInterface;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateType;
use Builderius\Bundle\TemplateBundle\Provider\TemplateSubType\BuilderiusTemplateSubTypesProviderInterface;

class BuilderiusTemplatePossibleTemplateTypesScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'possibleTemplateTypes';

    /**
     * @var BuilderiusTemplateSubTypesProviderInterface
     */
    private $templateSubTypesProvider;

    /**
     * @param BuilderiusTemplateSubTypesProviderInterface $templateSubTypesProvider
     */
    public function __construct(
        BuilderiusTemplateSubTypesProviderInterface $templateSubTypesProvider
    ) {
        $this->templateSubTypesProvider = $templateSubTypesProvider;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $result = [];
        foreach ($this->templateSubTypesProvider->getSubTypes('template') as $subType) {
            $result[$subType->getName()] = $this->convertType($subType);
        }
        
        return $result;
    }

    /**
     * @param BuilderiusTemplateSubTypeInterface $type
     * @return array
     */
    private function convertType(BuilderiusTemplateSubTypeInterface $type)
    {
        return [
            BuilderiusTemplateType::NAME_FIELD => $type->getName(),
            BuilderiusTemplateType::LABEL_FIELD => __($type->getLabel(), 'builderius')
        ];
    }
}
