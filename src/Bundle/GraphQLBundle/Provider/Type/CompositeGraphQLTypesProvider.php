<?php

namespace Builderius\Bundle\GraphQLBundle\Provider\Type;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;

class CompositeGraphQLTypesProvider implements GraphQLTypesProviderInterface
{
    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;
    
    /**
     * @var GraphQLTypesProviderInterface[]
     */
    private $providers = [];

    /**
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(BuilderiusRuntimeObjectCache $cache)
    {
        $this->cache = $cache;
    }

    public function addProvider(GraphQLTypesProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * @inheritDoc
     */
    public function getTypes()
    {
        $types = $this->cache->get('builderius_graphql_types');
        if (false === $types) {
            $types = [];
            foreach ($this->providers as $provider) {
                $types = array_merge($types, $provider->getTypes());
            }
            $this->cache->set('builderius_graphql_types', $types);
        }

        return $types;
    }

    /**
     * @inheritDoc
     */
    public function getType($name)
    {
        return $this->hasType($name) ? $this->getTypes()[$name] : null;
    }

    /**
     * @inheritDoc
     */
    public function hasType($name)
    {
        return isset($this->getTypes()[$name]);
    }
}