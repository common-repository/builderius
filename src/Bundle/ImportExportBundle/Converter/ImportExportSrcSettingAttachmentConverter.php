<?php

namespace Builderius\Bundle\ImportExportBundle\Converter;

class ImportExportSrcSettingAttachmentConverter extends AbstractImportExportSettingAttachmentConverter
{
    const SETTING_NAME = 'src';

    /**
     * @inheritDoc
     */
    public function convertOnImport($tempLocation, array $config)
    {
        if (isset($config['value']['a1']) && isset($config['value']['a1']['attachedFile'])) {
            $imgSrc = $this->processImportedImage($tempLocation, $config['value']['a1']);
            $config['value']['a1'] = $imgSrc;
        }

        return $config;
    }

    /**
     * @inheritDoc
     */
    public function convertOnExport(\ZipArchive $zip, array $config)
    {
        if (isset($config['value']['a1'])) {
            $baseUrl = wp_get_upload_dir()['baseurl'];
            if (strpos($config['value']['a1'], $baseUrl) !== false) {
                $imgCfg = $this->processExportedImage($zip, $config['value']['a1']);
                if (null !== $imgCfg) {
                    $config['value']['a1'] = $imgCfg;
                }
            }
        }

        return $config;
    }
}