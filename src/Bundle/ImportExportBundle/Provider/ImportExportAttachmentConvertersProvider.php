<?php

namespace Builderius\Bundle\ImportExportBundle\Provider;

use Builderius\Bundle\ImportExportBundle\Converter\ImportExportAttachmentConverterInterface;

class ImportExportAttachmentConvertersProvider implements ImportExportAttachmentConvertersProviderInterface
{
    /**
     * @var ImportExportAttachmentConverterInterface[]
     */
    private $converters = [];

    /**
     * @param ImportExportAttachmentConverterInterface $converter
     * @return $this
     */
    public function addConverter(ImportExportAttachmentConverterInterface $converter)
    {
        $this->converters[$converter->getSettingName()] = $converter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttachmentConverter($settingName)
    {
        if ($this->hasAttachmentConverter($settingName)) {
            return $this->converters[$settingName];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasAttachmentConverter($settingName)
    {
        return isset($this->converters[$settingName]);
    }
}