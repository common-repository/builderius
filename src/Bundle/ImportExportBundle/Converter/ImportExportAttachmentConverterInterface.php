<?php

namespace Builderius\Bundle\ImportExportBundle\Converter;

interface ImportExportAttachmentConverterInterface
{
    /**
     * @return string
     */
    public function getSettingName();

    /**
     * @param string $tempLocation
     * @param array $config
     * @return array
     */
    public function convertOnImport($tempLocation, array $config);

    /**
     * @param \ZipArchive $zip
     * @param array $config
     * @return array
     */
    public function convertOnExport(\ZipArchive $zip, array $config);
}