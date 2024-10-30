<?php

namespace Builderius\Bundle\SettingBundle\Factory\SettingValue\Chain\Element;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssValue;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValue;

class BuilderiusSettingCssValueFactoryChainElement extends AbstractBuilderiusSettingValueFactoryChainElement
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
        if (isset($arguments[BuilderiusSettingCssValue::MEDIA_QUERY_FIELD])) {
            $cssValueArguments[BuilderiusSettingCssValue::MEDIA_QUERY_FIELD] =
                $arguments[BuilderiusSettingCssValue::MEDIA_QUERY_FIELD];
        }
        if (isset($arguments[BuilderiusSettingCssValue::PSEUDO_CLASS_FIELD])) {
            $cssValueArguments[BuilderiusSettingCssValue::PSEUDO_CLASS_FIELD] =
                $arguments[BuilderiusSettingCssValue::PSEUDO_CLASS_FIELD];
        }

        return new BuilderiusSettingCssValue($cssValueArguments);
    }

    /**
     * @inheritDoc
     */
    protected function isApplicable(array $arguments)
    {
        return (isset($arguments[BuilderiusSettingCssAwareInterface::CSS_FIELD]) &&
            $arguments[BuilderiusSettingCssAwareInterface::CSS_FIELD] === true) ||
            isset($arguments[BuilderiusSettingCssValue::PSEUDO_CLASS_FIELD]) ||
            isset($arguments[BuilderiusSettingCssValue::MEDIA_QUERY_FIELD]);
    }
}
