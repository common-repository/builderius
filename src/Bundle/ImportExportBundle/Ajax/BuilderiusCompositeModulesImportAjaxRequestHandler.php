<?php

namespace Builderius\Bundle\ImportExportBundle\Ajax;

use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutor;
use Builderius\Bundle\ImportExportBundle\Event\ConfigImportEvent;
use Builderius\Bundle\ImportExportBundle\Provider\ImportExportAttachmentConvertersProviderInterface;
use Builderius\Bundle\TemplateBundle\Checker\ContentConfig\BuilderiusTemplateContentConfigCheckerInterface;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\Bundle\TemplateBundle\Helper\ContentConfigHelper;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;
use Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\RequestHandlerInterface;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;

class BuilderiusCompositeModulesImportAjaxRequestHandler implements RequestHandlerInterface
{
    const ACTION_NAME = 'builderius_composite_modules_import';

    /**
     * @var  BuilderiusTemplateConfigVersionConverterInterface
     */
    private $configVersionConverter;

    /**
     * @var BuilderiusTemplateContentConfigCheckerInterface
     */
    private $contentConfigChecker;

    /**
     * @var ImportExportAttachmentConvertersProviderInterface
     */
    private $ieAttachmentConvertersProvider;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var BuilderiusEntitiesGraphQLQueriesExecutor
     */
    private $graphqlExecutor;

    /**
     * @var string
     */
    private $tempLocation;

    /**
     * @param BuilderiusTemplateConfigVersionConverterInterface $configVersionConverter
     * @param BuilderiusTemplateContentConfigCheckerInterface $contentConfigChecker
     * @param ImportExportAttachmentConvertersProviderInterface $ieAttachmentConvertersProvider
     * @param EventDispatcher $eventDispatcher
     * @param BuilderiusEntitiesGraphQLQueriesExecutor $graphqlExecutor
     */
    public function __construct(
        BuilderiusTemplateConfigVersionConverterInterface $configVersionConverter,
        BuilderiusTemplateContentConfigCheckerInterface $contentConfigChecker,
        ImportExportAttachmentConvertersProviderInterface $ieAttachmentConvertersProvider,
        EventDispatcher $eventDispatcher,
        BuilderiusEntitiesGraphQLQueriesExecutor $graphqlExecutor
    ) {
        $this->configVersionConverter = $configVersionConverter;
        $this->contentConfigChecker = $contentConfigChecker;
        $this->ieAttachmentConvertersProvider = $ieAttachmentConvertersProvider;
        $this->eventDispatcher = $eventDispatcher;
        $this->graphqlExecutor = $graphqlExecutor;
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
        if (!isset($_FILES['file'])) {
            wp_send_json(['message' => __('missing required parameter "file"')], 400);
        }
        try {
            $config = $this->insert_template_package( $_FILES['file']['tmp_name'] );
            if ($config instanceof \WP_Error) {
                throw new \Exception($config->get_error_message(), 400);
            }
            $mutations = [];
            foreach ($config['modules'] as $i => $cModConf) {
                $itemConfig = $cModConf['config'];
                $itemConfig['template'] = [
                    'type' => 'template',
                    'technology' => $technology
                ];
                $itemConfig = $this->configVersionConverter->convert($itemConfig);
                $this->contentConfigChecker->check($itemConfig);
                unset($itemConfig['template']);
                $mutations[] =
                    [
                        'name' => 'saved_module_' . $i,
                        'query' => 'mutation {
                                createSavedCompositeModule(input: {
                                    label: "' . $cModConf['label'] .'",
                                    technology: ' . $technology . ',
                                    category: "' . $cModConf['category'] .'",
                                    tags: ' . json_encode($cModConf['tags']) .',
                                    serialized_config: "' . wp_slash(json_encode($itemConfig, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE)) .'",
                                    replace: true
                                }) {
                                   saved_composite_module {
                                      id
                                      name
                                      label 
                                      config
                                      category
                                      tags
                                      public
                                   }
                                }
                            }'
                    ];
            }
            $results = $this->graphqlExecutor->execute($mutations);
            $data = [];
            if (is_array($results)) {
                foreach ($results as $row) {
                    if (isset($row['data']) && !empty($row['data'])) {
                        $data[] = $row['data']['createSavedCompositeModule']['saved_composite_module'];
                    }
                }
            }
            wp_send_json_success($data);
        } catch (\Exception $e) {
            wp_send_json(
                ['message' => __(method_exists($e, 'getFullMessage') ? $e->getFullMessage() : $e->getMessage())],
                400
            );
        }
    }

    /**
     * @param string $file_path
     * @return mixed
     */
    public function insert_template_package($file_path)
    {
        $temp_folder_location = wp_get_upload_dir()['basedir'] . '/builderius/temp';
        $file_info = pathinfo($file_path);
        $newfilename = wp_unique_filename($temp_folder_location, $file_info['filename']);
        $this->tempLocation = sprintf('%s/%s', $temp_folder_location, $newfilename);

        $filesystem = $this->getFileSystem();
        if (!file_exists($this->tempLocation)) {
            $filesystem->mkdir($this->tempLocation);
        }

        $unzip = unzip_file($file_path, $this->tempLocation);
        if (is_wp_error($unzip)) {
            $filesystem->rmdir($this->tempLocation, true);

            return $unzip;
        }
        $this->tempLocation = trailingslashit($this->tempLocation);
        $content = ($filesystem->exists($this->tempLocation . 'content_config.json')) ? file_get_contents($this->tempLocation . 'content_config.json') : false;
        if (false === $content) {
            $filesystem->rmdir($this->tempLocation, true);
            return new \WP_Error('Import failed', __('No configuration file found inside uploaded zip.', 'builderius'));
        }
        $config = json_decode($content, true);
        if (empty($config)) {
            $filesystem->rmdir($this->tempLocation, true);
            return new \WP_Error('Import failed', __('Content config file is empty. There is nothing to import.', 'builderius'));
        }
        if (array_key_exists('modules', $config) && is_array($config['modules'])) {
            foreach ($config['modules'] as $i => $cModuleConfig) {
                if (is_array($cModuleConfig['config']) && is_array($cModuleConfig['config'][ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY])) {
                    foreach ($cModuleConfig['config'][ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY] as $ids => $modConf) {
                        if (is_array($modConf['settings'])) {
                            foreach ($modConf['settings'] as $k => $settConf) {
                                if ($this->ieAttachmentConvertersProvider->hasAttachmentConverter($settConf['name'])) {
                                    $converter = $this->ieAttachmentConvertersProvider->getAttachmentConverter($settConf['name']);
                                    $config['modules'][$i]['config'][ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$ids]['settings'][$k] =
                                        $converter->convertOnImport($this->tempLocation, $settConf);
                                }
                            }
                        }
                    }
                }
            }
        }
        $filesystem->rmdir($this->tempLocation, true);

        return $config;
    }

    /**
     * @return \WP_Filesystem_Base
     */
    public static function getFileSystem() {
        global $wp_filesystem;

        if ( ! $wp_filesystem ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            \WP_Filesystem();
        }

        return $wp_filesystem;
    }
}
