<?php

namespace Builderius\Bundle\TemplateBundle\GraphQL\TypeConfig;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLEnumTypeConfig;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLEnumValueConfig;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;

class TemplateTypeTypeConfig extends GraphQLEnumTypeConfig
{
    const NAME = 'BuilderiusTemplateTypeType';

    /**
     * @var BuilderiusTemplateTypesProviderInterface
     */
    private $templateTypesProvider;

    /**
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     * @return $this
     */
    public function setTemplateTypesProvider(BuilderiusTemplateTypesProviderInterface $templateTypesProvider)
    {
        $this->templateTypesProvider = $templateTypesProvider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @inheritDoc
     */
    public function getValues()
    {
        $values = [];
        foreach ($this->templateTypesProvider->getTypes() as $type) {
            $values[] = new GraphQLEnumValueConfig([
                GraphQLEnumValueConfig::NAME_FIELD => $type->getName(),
                GraphQLEnumValueConfig::VALUE_FIELD => $type->getName()
            ]);
        }

        return $values;
    }
}