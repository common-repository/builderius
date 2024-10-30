<?php

namespace Builderius\Bundle\ImportExportBundle\Converter;

class ImportExportListStyleSettingAttachmentConverter extends AbstractImportExportSettingAttachmentConverter
{
    const SETTING_NAME = 'listStyle';

    /**
     * @inheritDoc
     */
    public function convertOnImport($tempLocation, array $config)
    {
        foreach ($config['value'] as $n => $brkpConf) {
            foreach ($brkpConf as $l => $psdConf) {
                if (isset($psdConf['c1']) && isset($psdConf['c1']['attachedFile'])) {
                    $imgSrc = $this->processImportedImage($tempLocation, $psdConf['c1']);
                    $config['value'][$n][$l]['c1'] = $imgSrc;
                }
            }
        }

        return $config;
    }

    /**
     * @inheritDoc
     */
    public function convertOnExport(\ZipArchive $zip, array $config)
    {
        $baseUrl = wp_get_upload_dir()['baseurl'];
        foreach ($config['value'] as $n => $brkpConf) {
            foreach ($brkpConf as $l => $psdConf) {
                if (isset($psdConf['c1']) && strpos($psdConf['c1'], $baseUrl) !== false) {
                    $imgCfg = $this->processExportedImage($zip, $psdConf['c1']);
                    if (null !== $imgCfg) {
                        $config['value'][$n][$l]['c1'] = $imgCfg;
                    }
                }
            }
        }

        return $config;
    }
}