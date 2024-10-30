<?php

namespace Builderius\Bundle\GraphQLBundle\Provider\TypeConfig;

use Builderius\Bundle\GraphQLBundle\Config\GraphQLTypeConfigInterface;

interface GraphQLTypeConfigsProviderInterface
{
    /**
     * @return GraphQLTypeConfigInterface[]
     */
    public function getTypeConfigs();

    /**
     * @param string $name
     * @return GraphQLTypeConfigInterface
     */
    public function getTypeConfig($name);

    /**
     * @param string $name
     * @return bool
     */
    public function hasTypeConfig($name);
}