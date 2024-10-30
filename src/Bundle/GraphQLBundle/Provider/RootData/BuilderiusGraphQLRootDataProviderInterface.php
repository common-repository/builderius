<?php

namespace Builderius\Bundle\GraphQLBundle\Provider\RootData;

interface BuilderiusGraphQLRootDataProviderInterface
{
    /**
     * @param array $args
     * @return mixed
     */
    public function getRootData(array $args = []);
}