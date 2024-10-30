<?php

namespace Builderius\Bundle\TemplateBundle\GraphQL\TypeConfig;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLEnumTypeConfig;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLEnumValueConfig;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;

class TemplateTechnologyTypeConfig extends GraphQLEnumTypeConfig
{
    const NAME = 'BuilderiusTechnologyType';

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
        foreach ($this->templateTypesProvider->getTechnologies() as $technology) {
            $values[] = new GraphQLEnumValueConfig([
                GraphQLEnumValueConfig::NAME_FIELD => $technology->getName(),
                GraphQLEnumValueConfig::VALUE_FIELD => $technology->getName()
            ]);
        }

        return $values;
    }
}