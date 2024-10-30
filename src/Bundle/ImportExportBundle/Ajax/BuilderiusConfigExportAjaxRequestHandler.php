<?php

namespace Builderius\Bundle\ImportExportBundle\Ajax;

use Builderius\Bundle\ImportExportBundle\Provider\ImportExportAttachmentConvertersProviderInterface;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusBranchFromPostFactory;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\RequestHandlerInterface;
use Builderius\Symfony\Component\Filesystem\Filesystem;

class BuilderiusConfigExportAjaxRequestHandler implements RequestHandlerInterface
{
    const ACTION_NAME = 'builderius_config_export';
    const CONFIG_FILENAME = 'content_config.json';

    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusBranchFromPostFactory
     */
    private $branchFactory;

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
     * @param \WP_Query $wpQuery
     * @param BuilderiusBranchFromPostFactory $branchFactory
     * @param ImportExportAttachmentConvertersProviderInterface $ieAttachmentConvertersProvider
     */
    public function __construct(
        \WP_Query $wpQuery,
        BuilderiusBranchFromPostFactory $branchFactory,
        ImportExportAttachmentConvertersProviderInterface $ieAttachmentConvertersProvider
    ) {
        $this->wpQuery = $wpQuery;
        $this->branchFactory = $branchFactory;
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
        if (!isset($_POST['owner'])) {
            wp_send_json(['message' => __('missing required parameter "owner"')], 400);
        }
        if (!isset($_POST['branch'])) {
            wp_send_json(['message' => __('missing required parameter "branch"')], 400);
        }
        $branchPosts = $this->wpQuery->query([
            'post_type' => BuilderiusBranchPostType::POST_TYPE,
            'post_parent' => (int)$_POST['owner'],
            'name' => sanitize_text_field($_POST['branch']),
            'posts_per_page' => 1,
            'no_found_rows' => true,
            'post_status' => get_post_stati()
        ]);
        $config = [];
        if (count($branchPosts) > 0) {
            $branchPost = reset($branchPosts);
            $branch = $this->branchFactory->createBranch($branchPost);
            if (!isset($_POST['commit']) || in_array($_POST['commit'], [null, 'null', 'HEAD'])) {
                $config = $branch->getNotCommittedConfig();
            } else {
                $commit = $branch->getCommit(sanitize_text_field($_POST['commit']));
                if ($commit) {
                    $config = $commit->getContentConfig();
                }
            }
        }
        $this->createZip($_POST['owner'], (array)$config);
    }

    /**
     * @param int $ownerId
     * @param array $data_config
     * @return void|\WP_Error
     */
    public function createZip($ownerId, array $data_config)
    {
        if (!class_exists('ZipArchive')) {
            return new \WP_Error('Export failed', esc_html__('ZipArchive class is not installed on your server.', 'builderius'));
        }
        $this->zip = new \ZipArchive();
        $this->export_path = wp_get_upload_dir()['basedir'] . sprintf('/builderius/config_export_%d.zip', $ownerId);
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
