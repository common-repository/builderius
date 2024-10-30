<?php

namespace Builderius\Bundle\ImportExportBundle\Ajax;

use Builderius\Bundle\ImportExportBundle\Event\ConfigImportEvent;
use Builderius\Bundle\ImportExportBundle\Provider\ImportExportAttachmentConvertersProviderInterface;
use Builderius\Bundle\TemplateBundle\Checker\ContentConfig\BuilderiusTemplateContentConfigCheckerInterface;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\Bundle\TemplateBundle\Helper\ContentConfigHelper;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;
use Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\RequestHandlerInterface;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;

class BuilderiusConfigImportAjaxRequestHandler implements RequestHandlerInterface
{
    const ACTION_NAME = 'builderius_config_import';

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
     * @var string
     */
    private $tempLocation;

    /**
     * @param BuilderiusTemplateConfigVersionConverterInterface $configVersionConverter
     * @param BuilderiusTemplateContentConfigCheckerInterface $contentConfigChecker
     * @param ImportExportAttachmentConvertersProviderInterface $ieAttachmentConvertersProvider
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        BuilderiusTemplateConfigVersionConverterInterface $configVersionConverter,
        BuilderiusTemplateContentConfigCheckerInterface $contentConfigChecker,
        ImportExportAttachmentConvertersProviderInterface $ieAttachmentConvertersProvider,
        EventDispatcher $eventDispatcher
    ) {
        $this->configVersionConverter = $configVersionConverter;
        $this->contentConfigChecker = $contentConfigChecker;
        $this->ieAttachmentConvertersProvider = $ieAttachmentConvertersProvider;
        $this->eventDispatcher = $eventDispatcher;
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
        if (!isset($_FILES['file'])) {
            wp_send_json(['message' => __('missing required parameter "file"')], 400);
        }
        if (!isset($_POST['import_entity_id'])) {
            wp_send_json(['message' => __('missing required parameter "import_entity_id"')], 400);
        }
        try {
            $importEntityPost = get_post((int)$_POST['import_entity_id']);
            if (!$importEntityPost instanceof \WP_Post) {
                throw new \Exception('Not correct import_entity_id', 400);
            }
            $config = $this->insert_template_package( $_FILES['file']['tmp_name'] );
            if ($config instanceof \WP_Error) {
                throw new \Exception($config->get_error_message(), 400);
            }
            $config = $this->configVersionConverter->convert($config);
            $this->contentConfigChecker->check($config);
            $event = new ConfigImportEvent($config, $importEntityPost);
            $this->eventDispatcher->dispatch($event,'builderius_config_before_import');
            $config = $event->getConfig();
            if (isset($config['owner_type'])) {
                throw new \Exception('Unknown owner type', 400);
            }
            wp_send_json_success(ContentConfigHelper::formatConfig($config));
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
        if (is_array($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY])) {
            foreach ($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY] as $ids => $modConf) {
                if (is_array($modConf['settings'])) {
                    foreach ($modConf['settings'] as $k => $settConf) {
                        if ($this->ieAttachmentConvertersProvider->hasAttachmentConverter($settConf['name'])) {
                            $converter = $this->ieAttachmentConvertersProvider->getAttachmentConverter($settConf['name']);
                            $config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$ids]['settings'][$k] =
                                $converter->convertOnImport($this->tempLocation, $settConf);
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
                        $converter->convertOnImport($this->tempLocation, $settConf);
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
