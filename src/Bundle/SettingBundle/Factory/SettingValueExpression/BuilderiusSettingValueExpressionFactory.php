<?php

namespace Builderius\Bundle\SettingBundle\Factory\SettingValueExpression;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueExpression;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueExpressionsCollection;

class BuilderiusSettingValueExpressionFactory implements BuilderiusSettingValueExpressionFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(array $arguments)
    {
        return new BuilderiusSettingValueExpression($arguments);
    }

    /**
     * @inheritDoc
     */
    public function createCollection(array $arguments)
    {
        $expressions = [];
        foreach ($arguments as $data) {
            $expression = $this->create($data);
            $expressions[] = $expression;
        }

        return new BuilderiusSettingValueExpressionsCollection($expressions);
    }
}