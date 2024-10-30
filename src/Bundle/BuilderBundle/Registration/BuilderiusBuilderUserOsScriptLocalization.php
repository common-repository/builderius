<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

class BuilderiusBuilderUserOsScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'userOs';

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        if (stristr($_SERVER['HTTP_USER_AGENT'], 'mac')) {
            return 'mac';
        } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'linux')) {
            return 'linux';
        } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'windows')) {
            return 'windows';
        } else {
            return 'unknown';
        }
    }
}
