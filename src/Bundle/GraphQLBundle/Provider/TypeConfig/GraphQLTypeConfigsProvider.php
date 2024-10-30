<?php

namespace Builderius\Bundle\GraphQLBundle\Provider\TypeConfig;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLObjectTypeConfigInterface;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLTypeConfigInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;

class GraphQLTypeConfigsProvider implements GraphQLTypeConfigsProviderInterface
{
    const CACHE_KEY = 'builderius_graphql_type_configs';

    /**
     * @var GraphQLTypeConfigInterface[]
     */
    private $configs = [];

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param BuilderiusRuntimeObjectCache $cache
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(BuilderiusRuntimeObjectCache $cache, EventDispatcher $eventDispatcher)
    {
        $this->cache = $cache;
        $this->eventDispatcher = $eventDispatcher;
    }
    
    /**
     * @param Type $type
     * @return $this
     */
    public function addTypeConfig(GraphQLTypeConfigInterface $config)
    {
        $this->configs[$config->getName()] = $config;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTypeConfigs()
    {
        $configs = $this->cache->get(self::CACHE_KEY);
        if (false === $configs) {
           $configs = $this->configs;
           $event = new ConfigContainingEvent($configs);
           $this->eventDispatcher->dispatch($event, 'before_get_graphql_type_configs');
           $configs = $event->getConfig();
           usort(
             $configs,
             function(GraphQLTypeConfigInterface $a, GraphQLTypeConfigInterface $b) {
               if ($a instanceof GraphQLObjectTypeConfigInterface && !$b instanceof GraphQLObjectTypeConfigInterface) {
                   return 1;
               } elseif (!$a instanceof GraphQLObjectTypeConfigInterface && $b instanceof GraphQLObjectTypeConfigInterface) {
                   return -1;
               } elseif ($a instanceof GraphQLObjectTypeConfigInterface && $b instanceof GraphQLObjectTypeConfigInterface) {
                   $aTypeName = $a->getName();
                   foreach ($b->getFields() as $field) {
                       if ($aTypeName === str_replace('!', '', str_replace(']', '', str_replace('[', '', $field->getType())))) {
                           return -1;
                       } else {
                           foreach ($field->getArguments() as $argument) {
                               if ($aTypeName === str_replace('!', '', str_replace(']', '', str_replace('[', '', $argument->getType())))) {
                                   return -1;
                               }
                           }
                       }
                   }
                   $bTypeName = $b->getName();
                   foreach ($a->getFields() as $field) {
                       if ($bTypeName === str_replace('!', '', str_replace(']', '', str_replace('[', '', $field->getType())))) {
                           return 1;
                       } else {
                           foreach ($field->getArguments() as $argument) {
                               if ($bTypeName === str_replace('!', '', str_replace(']', '', str_replace('[', '', $argument->getType())))) {
                                   return 1;
                               }
                           }
                       }
                   }
               }

               return 0;
             }
           );
           $this->cache->set(self::CACHE_KEY, $configs);
        }

        return $configs;
    }

    /**
     * @inheritDoc
     */
    public function getTypeConfig($name)
    {
        return $this->hasTypeConfig($name) ? $this->configs[$name] : null;
    }

    /**
     * @inheritDoc
     */
    public function hasTypeConfig($name)
    {
        return isset($this->configs[$name]);
    }
}