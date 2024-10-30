<?php

namespace Builderius\Bundle\SettingBundle\Factory\SettingValueExpression;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueExpressionInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueExpressionsCollectionInterface;

interface BuilderiusSettingValueExpressionFactoryInterface
{
    /**
     * @param array $arguments
     * @return BuilderiusSettingValueExpressionInterface|null
     * @throws \Exception
     */
    public function create(array $arguments);

    /**
     * @param array $arguments
     * @return BuilderiusSettingValueExpressionsCollectionInterface|null
     * @throws \Exception
     */
    public function createCollection(array $arguments);
}
