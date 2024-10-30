<?php

namespace Builderius\Bundle\ImportExportBundle\Converter;

class ImportExportDataVarsSettingAttachmentConverter extends AbstractImportExportSettingAttachmentConverter
{
    const SETTING_NAME = 'dataVars';

    /**
     * @inheritDoc
     */
    public function convertOnImport($tempLocation, array $config)
    {
        if (is_array($config['value']['i1'])) {
            foreach ($config['value']['i1'] as $i => $settItm) {
                if (isset($settItm['a1']) && in_array($settItm['a1'], ['string', 'expression']) &&
                    isset($settItm['c1']['attachedFile'])) {
                    $imgSrc = $this->processImportedImage($tempLocation, $settItm['c1']);
                    $config['value']['i1'][$i]['c1'] = $imgSrc;
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
        if (is_array($config['value']['i1'])) {
            foreach ($config['value']['i1'] as $i => $settItm) {
                if (isset($settItm['a1']) && in_array($settItm['a1'], ['string', 'expression']) &&
                    strpos($settItm['c1'], $baseUrl) !== false) {
                    $imgCfg = $this->processExportedImage($zip, $settItm['c1']);
                    if (null !== $imgCfg) {
                        $config['value']['i1'][$i]['c1'] = $imgCfg;
                    }
                }
            }
        }

        return $config;
    }
}