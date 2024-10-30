<?php

namespace Builderius\Bundle\ImportExportBundle\Converter;

class ImportExportBackgroundImageSettingAttachmentConverter extends AbstractImportExportSettingAttachmentConverter
{
    const SETTING_NAME = 'backgroundImage';

    /**
     * @inheritDoc
     */
    public function convertOnImport($tempLocation, array $config)
    {
        foreach ($config['value'] as $n => $brkpConf) {
            foreach ($brkpConf as $l => $psdConf) {
                if (is_array($psdConf['i1'])) {
                    foreach ($psdConf['i1'] as $i => $settItm) {
                        if (isset($settItm['a1']) && isset($settItm['b1']) && $settItm['b1'] === 'image' &&
                            isset($settItm['a1']['attachedFile'])) {
                            $imgSrc = $this->processImportedImage($tempLocation, $settItm['a1']);
                            $config['value'][$n][$l]['i1'][$i]['a1'] = $imgSrc;
                        }
                    }
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
                if (is_array($psdConf['i1'])) {
                    foreach ($psdConf['i1'] as $i => $settItm) {
                        if (isset($settItm['b1']) && $settItm['b1'] === 'image' &&
                            strpos($settItm['a1'], $baseUrl) !== false) {
                            $imgCfg = $this->processExportedImage($zip, $settItm['a1']);
                            if (null !== $imgCfg) {
                                $config['value'][$n][$l]['i1'][$i]['a1'] = $imgCfg;
                            }
                        }
                    }
                }
            }
        }

        return $config;
    }
}