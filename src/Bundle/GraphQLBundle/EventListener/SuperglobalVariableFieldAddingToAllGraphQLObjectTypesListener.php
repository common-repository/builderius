<?php

namespace Builderius\Bundle\GraphQLBundle\EventListener;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLFieldConfigInterface;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLObjectTypeConfigInterface;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;

class SuperglobalVariableFieldAddingToAllGraphQLObjectTypesListener
{
    /**
     * @var GraphQLFieldConfigInterface
     */
    private $superglobalVariableFieldConfig;

    /**
     * @param GraphQLFieldConfigInterface $superglobalVariableFieldConfig
     */
    public function __construct(
        GraphQLFieldConfigInterface $superglobalVariableFieldConfig
    )
    {
        $this->superglobalVariableFieldConfig = $superglobalVariableFieldConfig;
    }

    /**
     * @param ConfigContainingEvent $event
     */
    public function beforeGetGraphqlTypeConfigs(ConfigContainingEvent $event)
    {
        $configs = $event->getConfig();
        foreach ($configs as &$config) {
            if ($config instanceof GraphQLObjectTypeConfigInterface) {
                $config->addField($this->superglobalVariableFieldConfig);
            }
        }
    }
}