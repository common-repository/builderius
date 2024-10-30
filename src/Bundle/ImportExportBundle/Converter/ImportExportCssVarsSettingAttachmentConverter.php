<?php

namespace Builderius\Bundle\ImportExportBundle\Converter;

class ImportExportCssVarsSettingAttachmentConverter extends AbstractImportExportSettingAttachmentConverter
{
    const SETTING_NAME = 'cssVars';

    /**
     * @inheritDoc
     */
    public function convertOnImport($tempLocation, array $config)
    {
        foreach ($config['value'] as $n => $brkpConf) {
            foreach ($brkpConf as $l => $psdConf) {
                if (is_array($psdConf['i1'])) {
                    foreach ($psdConf['i1'] as $i => $settItm) {
                        if (isset($settItm['a1']) && in_array($settItm['a1'], ['image', 'any-value']) &&
                            isset($settItm['b2']['attachedFile'])) {
                            $imgSrc = $this->processImportedImage($tempLocation, $settItm['b2']);
                            $config['value'][$n][$l]['i1'][$i]['b2'] = $imgSrc;
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
                        if (isset($settItm['a1']) && in_array($settItm['a1'], ['image', 'any-value']) &&
                            strpos($settItm['b2'], $baseUrl) !== false) {
                            $imgCfg = $this->processExportedImage($zip, $settItm['b2']);
                            if (null !== $imgCfg) {
                                $config['value'][$n][$l]['i1'][$i]['b2'] = $imgCfg;
                            }
                        }
                    }
                }
            }
        }

        return $config;
    }
}