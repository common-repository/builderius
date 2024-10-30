<?php

namespace Builderius\Bundle\SettingBundle\GraphQL\TypeConfig;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLEnumTypeConfig;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLEnumValueConfig;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;

class GlobalSettingsSetTypeTypeConfig extends GraphQLEnumTypeConfig
{
    const NAME = 'BuilderiusGlobalSettingsSetTypeType';

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
        $values[] = new GraphQLEnumValueConfig([
            GraphQLEnumValueConfig::NAME_FIELD => 'all',
            GraphQLEnumValueConfig::VALUE_FIELD => 'all'
        ]);
        foreach ($this->templateTypesProvider->getTypes() as $type) {
            $values[] = new GraphQLEnumValueConfig([
                GraphQLEnumValueConfig::NAME_FIELD => $type->getName(),
                GraphQLEnumValueConfig::VALUE_FIELD => $type->getName()
            ]);
        }

        return $values;
    }
}