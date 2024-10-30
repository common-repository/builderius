<?php

namespace Builderius\Bundle\ImportExportBundle\Ajax;

use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableSubModule;
use Builderius\Bundle\DeliverableBundle\Registration\BulderiusDeliverableSubModulePostType;
use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\ImportExportBundle\Provider\ImportExportAttachmentConvertersProviderInterface;
use Builderius\Bundle\ImportExportBundle\Provider\ImportExportImageSettingConvertersProviderInterface;
use Builderius\Bundle\ReleaseBundle\Registration\BulderiusReleasePostType;
use Builderius\Bundle\TemplateBundle\Checker\ContentConfig\BuilderiusTemplateContentConfigCheckerInterface;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateContentProviderInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;
use Builderius\MooMoo\Platform\Bundle\RequestBundle\Handler\RequestHandlerInterface;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;

class BuilderiusReleaseImportAjaxRequestHandler implements RequestHandlerInterface
{
    const ACTION_NAME = 'builderius_release_import';

    /**
     * @var BuilderiusTemplateContentProviderInterface
     */
    private $contentProvider;

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
     * @var BuilderiusEntitiesGraphQLQueriesExecutorInterface
     */
    private $graphQLQueriesExecutor;

    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var string
     */
    private $tempLocation;

    /**
     * @param BuilderiusTemplateContentProviderInterface $contentProvider
     * @param BuilderiusTemplateConfigVersionConverterInterface $configVersionConverter
     * @param BuilderiusTemplateContentConfigCheckerInterface $contentConfigChecker
     * @param ImportExportAttachmentConvertersProviderInterface $ieAttachmentConvertersProvider
     * @param EventDispatcher $eventDispatcher
     * @param BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor
     * @param \WP_Query $wpQuery
     */
    public function __construct(
        BuilderiusTemplateContentProviderInterface $contentProvider,
        BuilderiusTemplateConfigVersionConverterInterface $configVersionConverter,
        BuilderiusTemplateContentConfigCheckerInterface $contentConfigChecker,
        ImportExportAttachmentConvertersProviderInterface $ieAttachmentConvertersProvider,
        EventDispatcher $eventDispatcher,
        BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor,
        \WP_Query $wpQuery
    ) {
        $this->contentProvider = $contentProvider;
        $this->configVersionConverter = $configVersionConverter;
        $this->contentConfigChecker = $contentConfigChecker;
        $this->ieAttachmentConvertersProvider = $ieAttachmentConvertersProvider;
        $this->eventDispatcher = $eventDispatcher;
        $this->graphQLQueriesExecutor = $graphQLQueriesExecutor;
        $this->wpQuery = $wpQuery;
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
        try {
            $releaseConfig = $this->insert_template_package( $_FILES['file']['tmp_name'] );
            if ($releaseConfig instanceof \WP_Error) {
                throw new \Exception($releaseConfig->get_error_message(), 400);
            }
            $postsWithSameTag = $this->wpQuery->query([
                'post_type' => BulderiusReleasePostType::POST_TYPE,
                'title' => $releaseConfig['tag'],
                'posts_per_page' => 1,
                'no_found_rows' => true,
                'post_status' => get_post_stati()
            ]);
            if (!empty($postsWithSameTag)) {
                $releaseConfig['tag'] = sprintf('%s-hex%s', $releaseConfig['tag'], bin2hex(random_bytes(3)));
            }
            foreach ($releaseConfig['sub_modules'] as $key => $subModule) {
                if (isset($subModule['content_config']) && is_array($subModule['content_config'])) {
                    $config = $subModule['content_config'];
                    $config = $this->configVersionConverter->convert($config);
                    $this->contentConfigChecker->check($config);
                    $releaseConfig['sub_modules'][$key]['content_config'] = $config;
                }
            }
            $preparedPost = new \stdClass;
            $preparedPost->post_title = $releaseConfig['tag'];
            $preparedPost->post_type = BulderiusReleasePostType::POST_TYPE;
            $preparedPost->post_excerpt = $releaseConfig['description'];
            $preparedPost->post_status = 'draft';
            $preparedPost->post_author = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
            $preparedPost->post_date = current_time( 'mysql' );
            $preparedPost->post_date_gmt = $preparedPost->post_date;

            $postId = wp_insert_post(wp_slash((array)$preparedPost), true);

            if (is_wp_error($postId)) {
                /** @var \WP_Error $postId */
                if ('db_insert_error' === $postId->get_error_code()) {
                    throw new \Exception($postId->get_error_message(), 500);
                } else {
                    throw new \Exception($postId->get_error_message(), 400);
                }
            }

            $post = get_post($postId);
            foreach ($releaseConfig['sub_modules'] as $subModule) {
                $this->createDSMPost($post->ID, $subModule);
            }

            $this->eventDispatcher->dispatch(new PostContainingEvent($post), 'builderius_release_created');
            $queries = [
                [
                    'name' => 'release',
                    'query' => 'query {
                            release(id: ' . $post->ID . ') {
                                id
                                tag
                                description
                                status
                                sub_modules {
                                    id
                                    name
                                    type
                                    technology
                                    entity_type
                                }
                                created_at
                                author {
                                    display_name
                                }
                            }
                        }'
                ]
            ];
            $results = $this->graphQLQueriesExecutor->execute($queries);

            wp_send_json(
                $results['release']['data'],
                201
            );
        } catch (\Exception $e) {
            wp_send_json(
                ['message' => __(method_exists($e, 'getFullMessage') ? $e->getFullMessage() : $e->getMessage())],
                400
            );
        }
    }

    /**
     * @param int $releaseId
     * @param array $subModuleConfig
     */
    private function createDSMPost($releaseId, array $subModuleConfig)
    {
        $preparedPost = new \stdClass;
        $preparedPost->post_type = BulderiusDeliverableSubModulePostType::POST_TYPE;
        $preparedPost->post_title = $subModuleConfig['name'];
        $preparedPost->post_name = $subModuleConfig['name'];;
        $preparedPost->post_excerpt = json_encode([
            BuilderiusDeliverableSubModule::TYPE_FIELD => $subModuleConfig['type'],
            BuilderiusDeliverableSubModule::TECHNOLOGY_FIELD => $subModuleConfig['technology'],
            BuilderiusDeliverableSubModule::ENTITY_TYPE_FIELD => $subModuleConfig['entity_type']
        ]);
        $preparedPost->post_parent = $releaseId;
        $preparedPost->post_author = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
        $preparedPost->post_date = current_time( 'mysql' );
        $preparedPost->post_date_gmt = $preparedPost->post_date;

        $postId = wp_insert_post(wp_slash((array)$preparedPost), true);

        if (is_wp_error($postId)) {
            /** @var \WP_Error $postId */
            if ('db_insert_error' === $postId->get_error_code()) {
                throw new \Exception($postId->get_error_message(), 500);
            } else {
                throw new \Exception($postId->get_error_message(), 400);
            }
        }
        $post = get_post($postId);
        $post->post_content = json_encode($this->contentProvider->getContent($subModuleConfig['technology'], $subModuleConfig['content_config']), JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
        remove_all_filters('content_save_pre');
        wp_update_post($post);
        update_post_meta(
            $postId,
            BuilderiusDeliverableSubModule::CONTENT_CONFIG_FIELD,
            wp_slash(json_encode($subModuleConfig['content_config'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE))
        );
        update_post_meta(
            $postId,
            BuilderiusDeliverableSubModule::ATTRIBUTES_FIELD,
            wp_slash(json_encode($subModuleConfig['attributes'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE))
        );
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
        $content = ($filesystem->exists($this->tempLocation . 'release_config.json')) ? file_get_contents($this->tempLocation . 'release_config.json') : false;
        if (false === $content) {
            $filesystem->rmdir($this->tempLocation, true);
            return new \WP_Error('Import failed', __('No configuration file found inside uploaded zip.', 'builderius'));
        }
        $releaseConfig = json_decode($content, true);
        $event = new ConfigContainingEvent($releaseConfig);
        $this->eventDispatcher->dispatch($event, 'builderius_release_config_before_import');
        $releaseConfig = $event->getConfig();
        if (empty($releaseConfig) || empty($releaseConfig['sub_modules'])) {
            $filesystem->rmdir($this->tempLocation, true);
            return new \WP_Error('Import failed', __('Release config file is empty. There is nothing to import.', 'builderius'));
        }
        foreach ($releaseConfig['sub_modules'] as $key => $subModule) {
            if (isset($subModule['content_config']) && is_array($subModule['content_config'])) {
                $config = $subModule['content_config'];
                if (isset($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY]) && is_array($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY])) {
                    foreach ($config[ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY] as $ids => $modConf) {
                        if (is_array($modConf['settings'])) {
                            foreach ($modConf['settings'] as $k => $settConf) {
                                if ($this->ieAttachmentConvertersProvider->hasAttachmentConverter($settConf['name'])) {
                                    $converter = $this->ieAttachmentConvertersProvider->getAttachmentConverter($settConf['name']);
                                    $releaseConfig['sub_modules'][$key]['content_config'][ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY][$ids]['settings'][$k] =
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
                            $releaseConfig['sub_modules'][$key]['content_config']['template']['settings'][$k] =
                                $converter->convertOnImport($this->tempLocation, $settConf);
                        }
                    }
                }
            }
        }
        $filesystem->rmdir($this->tempLocation, true);

        return $releaseConfig;
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
