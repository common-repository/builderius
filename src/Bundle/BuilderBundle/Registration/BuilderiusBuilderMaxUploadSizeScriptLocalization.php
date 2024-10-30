<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

class BuilderiusBuilderMaxUploadSizeScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'maxUploadSize';
    const SIZE = 'size';
    const UNIT = 'unit';

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        return [
            self::SIZE => wp_max_upload_size(),
            self::UNIT => 'B'
        ];
    }
}
