<?php

namespace Builderius\Bundle\ImportExportBundle\Converter;

class ImportExportHtmlAttributeSettingAttachmentConverter extends AbstractImportExportSettingAttachmentConverter
{
    const SETTING_NAME = 'htmlAttribute';

    /**
     * @inheritDoc
     */
    public function convertOnImport($tempLocation, array $config)
    {
        if (is_array($config['value']['i1'])) {
            foreach ($config['value']['i1'] as $i => $settItm) {
                if (isset($settItm['b1']['attachedFile'])) {
                    $imgSrc = $this->processImportedImage($tempLocation, $settItm['b1']);
                    $config['value']['i1'][$i]['b1'] = $imgSrc;
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
                if (isset($settItm['b1']) && strpos($settItm['b1'], $baseUrl) !== false) {
                    $imgCfg = $this->processExportedImage($zip, $settItm['b1']);
                    if (null !== $imgCfg) {
                        $config['value']['i1'][$i]['b1'] = $imgCfg;
                    }
                }
            }
        }

        return $config;
    }
}