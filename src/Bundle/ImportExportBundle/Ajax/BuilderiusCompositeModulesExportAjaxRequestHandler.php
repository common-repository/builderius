<?php

namespace Builderius\Bundle\ImportExportBundle\Ajax;

use Builderius\Bundle\CategoryBundle\Provider\BuilderiusCategoriesProviderInterface;
use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\ImportExportBundle\Provider\ImportExportAttachmentConvertersProviderInterface;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\RequestHandlerInterface;
use Builderius\Symfony\Component\Filesystem\Filesystem;

class BuilderiusCompositeModulesExportAjaxRequestHandler implements RequestHandlerInterface
{
    const ACTION_NAME = 'builderius_composite_modules_export';
    const CONFIG_FILENAME = 'content_config.json';

    /**
     * @var BuilderiusEntitiesGraphQLQueriesExecutorInterface
     */
    private $graphQLQueriesExecutor;

    /**
     * @var BuilderiusTemplateConfigVersionConverterInterface
     */
    private $configVersionConverter;

    /**
     * @var BuilderiusCategoriesProviderInterface
     */
    private $categoriesProvider;

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
     * @param BuilderiusTemplateConfigVersionConverterInterface $configVersionConverter
     * @param BuilderiusCategoriesProviderInterface $categoriesProvider
     * @param ImportExportAttachmentConvertersProviderInterface $ieAttachmentConvertersProvider
     */
    public function __construct(
        BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor,
        BuilderiusTemplateConfigVersionConverterInterface $configVersionConverter,
        BuilderiusCategoriesProviderInterface $categoriesProvider,
        ImportExportAttachmentConvertersProviderInterface $ieAttachmentConvertersProvider
    ) {
        $this->graphQLQueriesExecutor = $graphQLQueriesExecutor;
        $this->configVersionConverter = $configVersionConverter;
        $this->categoriesProvider = $categoriesProvider;
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
        if (!isset($_POST['technology'])) {
            wp_send_json(['message' => __('missing required parameter "technology"')], 400);
        }
        $technology = sanitize_text_field($_POST['technology']);
        $config = $this->getSavedCompositeModules($technology);

        $this->createZip($technology, (array)$config);
    }


    /**
     * @param string $technology
     * @return array
     */
    private function getSavedCompositeModules($technology)
    {
        $result = [];
        $queries = [
            [
                'name' => 'saved_composite_modules',
                'query' => 'query {
                                modules: saved_composite_modules(technology:' . $technology . ') {
                                    label
                                    config
                                    category
                                    tags
                                    icon
                                }
                            }'
            ]
        ];
        $results = $this->graphQLQueriesExecutor->execute($queries);

        $data = $results['saved_composite_modules']['data'];
        if (isset($data['modules'])) {
            $result['technology'] = $technology;
            $categories = [];
            $result['modules'] = [];
            foreach ($data['modules'] as $item) {
                if ($this->categoriesProvider->hasCategory('module', $item['category'])) {
                    $category = [
                        'name' => $item['category'],
                        'label' => $this->categoriesProvider->getCategory('module', $item['category'])->getLabel()
                    ];
                    if (!in_array($category, $categories)) {
                        $categories[] = $category;
                    }
                }
                $result['modules'][] = [
                    'label' => $item['label'],
                    'config' => $this->configVersionConverter->convert($item['config']),
                    'category' => $item['category'],
                    'tags' => $item['tags'],
                    'icon' => $item['icon']
                ];
            }
            if (!empty($categories)) {
                $result['categories'] = $categories;
            }
        }

        return $result;
    }

    /**
     * @param string $technology
     * @param array $data_config
     * @return void|\WP_Error
     */
    public function createZip($technology, array $data_config)
    {
        if (!class_exists('ZipArchive')) {
            return new \WP_Error('Export failed', esc_html__('ZipArchive class is not installed on your server.', 'builderius'));
        }
        $this->zip = new \ZipArchive();
        $this->export_path = wp_get_upload_dir()['basedir'] . sprintf('/builderius/%s_composite_modules_export.zip', $technology);
        $open_zip = $this->zip->open($this->export_path, \ZIPARCHIVE::CREATE | \ZipArchive::OVERWRITE);

        if (true !== $open_zip) {
            return new \WP_Error(
                'Export failed',
                sprintf(esc_html__('Could not create the export file in %s', 'builderius'), $this->export_path)
            );
        }
        $data_config = $this->addImages($data_config);
        $this->zip->addFromString(self::CONFIG_FILENAME, wp_json_encode($data_config, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE));
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
        foreach ($config['modules'] as $i => $moduleData) {
            if (is_array($moduleData['config'][ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY])) {
                foreach ($moduleData['config'][ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY] as $ids => $modConf) {
                    if (is_array($modConf['settings'])) {
                        foreach ($modConf['settings'] as $k => $settConf) {
                            if ($this->ieAttachmentConvertersProvider->hasAttachmentConverter($settConf['name'])) {
                                $converter = $this->ieAttachmentConvertersProvider->getAttachmentConverter($settConf['name']);
                                $config['modules'][$i]['config'][ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$ids]['settings'][$k] =
                                    $converter->convertOnExport($this->zip, $settConf);
                            }
                        }
                    }
                }
            }
        }

        return $config;
    }
}
