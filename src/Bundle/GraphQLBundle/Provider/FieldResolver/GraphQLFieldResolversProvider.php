<?php

namespace Builderius\Bundle\GraphQLBundle\Provider\FieldResolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;

class GraphQLFieldResolversProvider implements GraphQLFieldResolversProviderInterface
{
    private $resolvers = [];

    /**
     * @param GraphQLFieldResolverInterface $resolver
     * @return $this
     */
    public function addResolver(GraphQLFieldResolverInterface $resolver)
    {
        if(!empty($resolver->getTypeNames())) {
            foreach ($resolver->getTypeNames() as $typeName) {
                $this->resolvers[$typeName][$resolver->getFieldName()][] = $resolver;
            }
        } else {
            $this->resolvers['all_types'][$resolver->getFieldName()][] = $resolver;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getResolvers($typeName, $fieldName)
    {
        if ($this->hasResolvers($typeName, $fieldName)) {
            if (isset($this->resolvers[$typeName][$fieldName])) {
                $resolvers = $this->resolvers[$typeName][$fieldName];
            } else {
                $resolvers = $this->resolvers['all_types'][$fieldName];
            }

            usort($resolvers, function(GraphQLFieldResolverInterface $a, GraphQLFieldResolverInterface $b) {
                $aSortOrder = $a->getSortOrder();
                $bSortOrder = $b->getSortOrder();
                if ($aSortOrder < $bSortOrder) {
                    return -1;
                } elseif ($aSortOrder > $bSortOrder) {
                    return 1;
                } else {
                    return 0;
                }
            });

            return $resolvers;
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    public function hasResolvers($typeName, $fieldName)
    {
        return isset($this->resolvers[$typeName][$fieldName]) || isset($this->resolvers['all_types'][$fieldName]);
    }
}