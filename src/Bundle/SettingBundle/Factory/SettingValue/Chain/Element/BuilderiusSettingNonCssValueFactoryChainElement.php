<?php

namespace Builderius\Bundle\SettingBundle\Factory\SettingValue\Chain\Element;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValue;

class BuilderiusSettingNonCssValueFactoryChainElement extends AbstractBuilderiusSettingValueFactoryChainElement
{
    /**
     * @inheritDoc
     */
    protected function createValue(array $arguments)
    {
        $cssValueArguments = [];
        $cssValueArguments[BuilderiusSettingValue::VALUE_FIELD] = $arguments[BuilderiusSettingValue::VALUE_FIELD];
        if (isset($arguments[BuilderiusSettingValue::DEFAULT_FIELD])) {
            $cssValueArguments[BuilderiusSettingValue::DEFAULT_FIELD] =
                $arguments[BuilderiusSettingValue::DEFAULT_FIELD];
        }

        return new BuilderiusSettingValue($cssValueArguments);
    }

    /**
     * @inheritDoc
     */
    protected function isApplicable(array $arguments)
    {
        return !isset($arguments[BuilderiusSettingCssAwareInterface::CSS_FIELD]) ||
            $arguments[BuilderiusSettingCssAwareInterface::CSS_FIELD] === false;
    }
}
