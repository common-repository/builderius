<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\ScriptLocalizationInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareTrait;

abstract class AbstractBuilderiusBuilderScriptLocalization implements ScriptLocalizationInterface, ConditionAwareInterface
{
    use ConditionAwareTrait;

    const OBJECT_NAME = 'builderiusBackend';
    const PROPERTY_NAME = null;
    
    /**
     * @inheritDoc
     */
    public function getObjectName()
    {
        return static::OBJECT_NAME;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyName()
    {
        return static::PROPERTY_NAME;
    }

    public function getSortOrder()
    {
        return 10;
    }
}
