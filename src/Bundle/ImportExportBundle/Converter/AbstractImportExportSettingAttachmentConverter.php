<?php

namespace Builderius\Bundle\ImportExportBundle\Converter;

abstract class AbstractImportExportSettingAttachmentConverter implements ImportExportAttachmentConverterInterface
{
    const SETTING_NAME = null;

    /**
     * @inheritDoc
     */
    public function getSettingName()
    {
        return static::SETTING_NAME;
    }

    /**
     * @param string $tempLocation
     * @param array $config
     * @return string
     */
    protected function processImportedImage($tempLocation, array $config)
    {
        global $wpdb;
        $sql = sprintf(
            "SELECT post_id, meta_value FROM %s WHERE meta_key = '_wp_attachment_metadata' AND meta_value LIKE '%%%s%%'",
            $wpdb->postmeta,
            $config['attachedFile']
        );

        $results = $wpdb->get_results($sql);
        if (empty($results)) {
            $sql = sprintf(
                "SELECT post_id, meta_value FROM %s WHERE meta_key = '_wp_attached_file' AND meta_value LIKE '%%%s%%'",
                $wpdb->postmeta,
                $config['attachedFile']
            );

            $results = $wpdb->get_results($sql);
        }
        if (empty($results)) {
            $tempImgPath = $tempLocation . 'files/' . $config['attachedFile'];
            if (!file_exists($tempImgPath)) {
                $tempImgPath = $tempLocation . 'images/' . $config['attachedFile'];
            }
            $fileArray = array(
                'name' => $config['attachedFile'],
                'tmp_name' => $tempImgPath
            );
            add_filter(
                'wp_handle_sideload_overrides',
                function (array $overrides){
                    $overrides['unique_filename_callback'] = function ($dir, $filename) {
                        return $filename;
                    };

                    return $overrides;
                },
                10,
                2
            );
            $id = media_handle_sideload($fileArray);
        } else {
            $id = (int)reset($results)->post_id;
        }
        if ($id > 0) {
            $fullFilePath = wp_get_attachment_url($id);
            if ($config['originalSize'] === true) {
                return $fullFilePath;
            } else {
                $meta = wp_get_attachment_metadata($id);
                $configSize = $config['size'];
                foreach ($meta['sizes'] as $sizeName => $sizeData) {
                    if ($sizeData['width'] === $configSize['width'] && $sizeData['height'] === $configSize['height']) {
                        return wp_get_attachment_image_url( $id, $sizeName );
                    }
                }
                $sizeName = sprintf('%sx%s', $configSize['width'], $configSize['height']);
                add_image_size(
                    $sizeName,
                    $configSize['width'],
                    $configSize['height']
                );
                wp_create_image_subsizes($fullFilePath, $id);

                return wp_get_attachment_image_url( $id, $sizeName );
            }
        }

        return null;
    }

    /**
     * @param \ZipArchive $zip
     * @param string $url
     * @return array|null
     */
    protected function processExportedImage(\ZipArchive $zip, $url)
    {
        $originalSize = false;
        $arr = explode('/', $url);
        $fileName = end($arr);
        $id = attachment_url_to_postid($url);
        if (0 !== $id) {
            $originalSize = true;
        } else {
            global $wpdb;

            $sql = sprintf(
                "SELECT post_id, meta_value FROM %s WHERE meta_key = '_wp_attachment_metadata' AND meta_value LIKE '%%%s%%'",
                $wpdb->postmeta,
                $fileName
            );

            $results = $wpdb->get_results($sql);
            if ($results) {
                $id = (int)reset($results)->post_id;
            }
        }
        if ($id > 0) {
            $fullFilePath = get_attached_file($id);
            $arr = explode('/', $fullFilePath);
            $oFileName = end($arr);
            if (is_file($fullFilePath)) {
                $imgCfg = [];
                $zip->addFile($fullFilePath, 'files/' . $oFileName);
                $imgCfg['attachedFile'] = $oFileName;
                if ($originalSize) {
                    $imgCfg['originalSize'] = true;
                } else {
                    $imgCfg['originalSize'] = false;
                    $meta = wp_get_attachment_metadata($id);
                    foreach ($meta['sizes'] as $sizeName => $sizeData) {
                        if ($sizeData['file'] === $fileName) {
                            $imgCfg['size'] = [
                                'name' => $sizeName,
                                'width' => $sizeData['width'],
                                'height' => $sizeData['height']
                            ];
                            break;
                        }
                    }
                }

                return $imgCfg;
            }
        }

        return null;
    }
}