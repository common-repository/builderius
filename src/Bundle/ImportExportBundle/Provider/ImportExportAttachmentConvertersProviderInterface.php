<?php

namespace Builderius\Bundle\ImportExportBundle\Provider;

use Builderius\Bundle\ImportExportBundle\Converter\ImportExportAttachmentConverterInterface;

interface ImportExportAttachmentConvertersProviderInterface
{
    /**
     * @param string $settingName
     * @return ImportExportAttachmentConverterInterface|null
     */
    public function getAttachmentConverter($settingName);

    /**
     * @param string $settingName
     * @return bool
     */
    public function hasAttachmentConverter($settingName);
}