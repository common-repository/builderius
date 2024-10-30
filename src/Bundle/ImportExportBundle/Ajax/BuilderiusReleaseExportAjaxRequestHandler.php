<?php

namespace Builderius\Bundle\ImportExportBundle\Ajax;

use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\ImportExportBundle\Provider\ImportExportAttachmentConvertersProviderInterface;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;
use Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\RequestHandlerInterface;
use Builderius\Symfony\Component\Filesystem\Filesystem;

class BuilderiusReleaseExportAjaxRequestHandler implements RequestHandlerInterface
{
    const ACTION_NAME = 'builderius_release_export';
    const CONFIG_FILENAME = 'release_config.json';

    /**
     * @var BuilderiusEntitiesGraphQLQueriesExecutorInterface
     */
    private $graphQLQueriesExecutor;

    /**
     * @var ImportExportAttachmentConvertersProviderInterface
     */
    private $ieAttachmentConvertersProvider;

    /**
     * @var \ZipArchive
     */
    private $zip;

    /**
     * @var string
     */
    private $export_path;

    /**
     * @param BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor
     * @param ImportExportAttachmentConvertersProviderInterface $ieAttachmentConvertersProvider
     */
    public function __construct(
        BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor,
        ImportExportAttachmentConvertersProviderInterface $ieAttachmentConvertersProvider
    ) {
        $this->graphQLQueriesExecutor = $graphQLQueriesExecutor;
        $this->ieAttachmentConvertersProvider = $ieAttachmentConvertersProvider;
    }

    /**
     * @inheritDoc
     */
    public function getActionName()
    {
        return self::ACTION_NAME;
    }

    /**
     * @inheritDoc
     */
    public function isAjax()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isPrivileged()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function handle()
    {
        check_ajax_referer('builderius_ajax_nonce', 'nonce');
        if (!isset($_POST['id'])) {
            wp_send_json(['message' => __('missing required parameter "id"')], 400);
        }

        $queries = [
            [
                'name' => 'release',
                'query' => 'query {
                            release(id: ' . $_POST['id'] . ') {
                                tag
                                description
                                sub_modules {
                                    name
                                    type
                                    technology
                                    entity_type
                                    attributes
                                    content_config
                                }
                            }
                        }'
            ]
        ];
        $results = $this->graphQLQueriesExecutor->execute($queries);

        $releaseData = $results['release']['data']['release'];
        if ($releaseData) {
            $this->createZip($_POST['id'], (array)$releaseData);
        }
    }

    /**
     * @param int $releaseId
     * @param array $releaseData
     * @return void|\WP_Error
     */
    public function createZip($releaseId, array $releaseData)
    {
        if (!class_exists('ZipArchive')) {
            return new \WP_Error('Export failed', esc_html__('ZipArchive class is not installed on your server.', 'builderius'));
        }
        $this->zip = new \ZipArchive();
        $this->export_path = wp_get_upload_dir()['basedir'] . sprintf('/builderius/release_export_%d.zip', $releaseId);
        $open_zip = $this->zip->open($this->export_path, \ZIPARCHIVE::CREATE | \ZipArchive::OVERWRITE);

        if (true !== $open_zip) {
            return new \WP_Error(
                'Export failed',
                sprintf(esc_html__('Could not create the export file in %s', 'builderius'), $this->export_path)
            );
        }
        foreach ($releaseData['sub_modules'] as $k => $subModule) {
            $config = is_array($subModule['content_config']) ? $subModule['content_config'] : [];
            $releaseData['sub_modules'][$k]['content_config'] = $this->addImages($config);
        }
        $this->zip->addFromString(self::CONFIG_FILENAME, wp_json_encode($releaseData, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE));
        $this->zip->close();
        $archive_file_name = basename($this->export_path);

        header('Content-type: application/zip');
        header('Content-Disposition: attachment; filename=' . $archive_file_name);
        header('Content-Length: ' . filesize($this->export_path));
        header('Pragma: no-cache');
        header('Expires: 0');
        $filesystem = new Filesystem();
        echo file_get_contents($this->export_path);
        $filesystem->remove($this->export_path);
        exit();
    }

    /**
     * @param array $config
     * @return array
     */
    private function addImages(array $config)
    {
        if (is_array($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY])) {
            foreach ($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY] as $ids => $modConf) {
                if (is_array($modConf['settings'])) {
                    foreach ($modConf['settings'] as $k => $settConf) {
                        if ($this->ieAttachmentConvertersProvider->hasAttachmentConverter($settConf['name'])) {
                            $converter = $this->ieAttachmentConvertersProvider->getAttachmentConverter($settConf['name']);
                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$ids]['settings'][$k] =
                                $converter->convertOnExport($this->zip, $settConf);
                        }
                    }
                }
            }
        }
        if (is_array($config['template']) && is_array($config['template']['settings'])) {
            foreach ($config['template']['settings'] as $k => $settConf) {
                if ($this->ieAttachmentConvertersProvider->hasAttachmentConverter($settConf['name'])) {
                    $converter = $this->ieAttachmentConvertersProvider->getAttachmentConverter($settConf['name']);
                    $config['template']['settings'][$k] =
                        $converter->convertOnExport($this->zip, $settConf);
                }
            }
        }

        return $config;
    }
}
