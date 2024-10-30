<?php

namespace Builderius\Bundle\GraphQLBundle\EventListener;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLFieldConfigInterface;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLObjectTypeConfigInterface;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;

class OptionValueFieldAddingToAllGraphQLObjectTypesListener
{
    /**
     * @var GraphQLFieldConfigInterface
     */
    private $optionValueFieldConfig;

    /**
     * @param GraphQLFieldConfigInterface $optionValueFieldConfig
     */
    public function __construct(
        GraphQLFieldConfigInterface $optionValueFieldConfig
    )
    {
        $this->optionValueFieldConfig = $optionValueFieldConfig;
    }

    /**
     * @param ConfigContainingEvent $event
     */
    public function beforeGetGraphqlTypeConfigs(ConfigContainingEvent $event)
    {
        $configs = $event->getConfig();
        foreach ($configs as &$config) {
            if ($config instanceof GraphQLObjectTypeConfigInterface) {
                $config->addField($this->optionValueFieldConfig);
            }
        }
    }
}