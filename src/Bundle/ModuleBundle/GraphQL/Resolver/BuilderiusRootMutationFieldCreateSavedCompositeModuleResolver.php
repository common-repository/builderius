<?php

namespace Builderius\Bundle\ModuleBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\ModuleBundle\Factory\BuilderiusSavedCompositeModuleFromPostFactory;
use Builderius\Bundle\ModuleBundle\Model\BuilderiusSavedCompositeModule;
use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\ModuleBundle\Registration\BuilderiusSavedCompositeModulePostType;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentTagTaxonomy;
use Builderius\Bundle\TemplateBundle\Checker\ContentConfig\BuilderiusTemplateContentConfigCheckerInterface;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\Bundle\TemplateBundle\Event\ObjectContainingEvent;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldCreateSavedCompositeModuleResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var BuilderiusTemplateContentConfigCheckerInterface
     */
    private $configChecker;

    /**
     * @var BuilderiusModulesProviderInterface
     */
    private $modulesProvider;

    /**
     * @var BuilderiusModulesProviderInterface
     */
    private $compositeModulesProvider;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusTemplateContentConfigCheckerInterface $configChecker
     * @param BuilderiusModulesProviderInterface $modulesProvider
     * @param BuilderiusModulesProviderInterface $compositeModulesProvider
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        BuilderiusTemplateContentConfigCheckerInterface $configChecker,
        BuilderiusModulesProviderInterface $modulesProvider,
        BuilderiusModulesProviderInterface $compositeModulesProvider
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->configChecker = $configChecker;
        $this->modulesProvider = $modulesProvider;
        $this->compositeModulesProvider = $compositeModulesProvider;
    }

    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['BuilderiusRootMutation'];
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'createSavedCompositeModule';
    }

    /**
     * @inheritDoc
     */
    public function isApplicable($objectValue, array $args, $context, ResolveInfo $info)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $input = $args['input'];
        $preparedPost = $this->getPreparedPost($input);

        $time = current_time( 'mysql' );
        $preparedPost->post_date = $time;
        $preparedPost->post_date_gmt = get_gmt_from_date($time);
        $event = new ObjectContainingEvent($preparedPost);
        $this->eventDispatcher->dispatch(
            $event,
            'builderius_saved_composite_module_before_create'
        );

        if ($event->getError() && is_wp_error($event->getError())) {
            throw new \Exception($event->getError()->get_error_message(), 400);
        }
        $preparedPost = $event->getObject();
        if (!property_exists($preparedPost, 'ID')) {
            $postId = wp_insert_post(wp_slash((array)$preparedPost), true);
        } else {
            $postId = wp_update_post(wp_slash((array)$preparedPost), true);
        }
        if (is_wp_error($postId)) {
            /** @var \WP_Error $postId */
            if ('db_insert_error' === $postId->get_error_code()) {
                throw new \Exception($postId->get_error_message(), 500);
            } else {
                throw new \Exception($postId->get_error_message(), 400);
            }
        }
        $post = get_post($postId);
        if (property_exists($preparedPost, BuilderiusSavedCompositeModule::CONFIG_FIELD)) {
            update_post_meta(
                $postId,
                BuilderiusSavedCompositeModule::CONFIG_FIELD,
                wp_slash($preparedPost->{BuilderiusSavedCompositeModule::CONFIG_FIELD})
            );
        }
        if (property_exists($preparedPost, BuilderiusSavedCompositeModule::ICON_FIELD)) {
            update_post_meta(
                $postId,
                BuilderiusSavedCompositeModule::ICON_FIELD,
                $preparedPost->{BuilderiusSavedCompositeModule::ICON_FIELD}
            );
        }
        if (property_exists($preparedPost, BuilderiusSavedCompositeModule::CATEGORY_FIELD)) {
            update_post_meta(
                $postId,
                BuilderiusSavedCompositeModule::CATEGORY_FIELD,
                $preparedPost->{BuilderiusSavedCompositeModule::CATEGORY_FIELD}
            );
        }
        if (property_exists($preparedPost, BuilderiusSavedCompositeModule::TAGS_FIELD)) {
            update_post_meta(
                $postId,
                BuilderiusSavedCompositeModule::TAGS_FIELD,
                $preparedPost->{BuilderiusSavedCompositeModule::TAGS_FIELD}
            );
        }
        $technologyTermsUpdate = $this->handleTerms(
            $post->ID,
            [$preparedPost->technology],
            BuilderiusTemplateTechnologyTaxonomy::NAME
        );

        if (is_wp_error($technologyTermsUpdate)) {
            throw new \Exception($technologyTermsUpdate->get_error_message(), 400);
        }

        $this->eventDispatcher->dispatch(new PostContainingEvent($post), 'builderius_saved_composite_module_created');

        return new \ArrayObject(['saved_composite_module' => BuilderiusSavedCompositeModuleFromPostFactory::createSavedCompositeModule($post)]);
    }

    /**
     * @param array $args
     * @return false|mixed|\stdClass
     * @throws \Exception
     */
    protected function getPreparedPost(array $args)
    {
        $prepared_post = new \stdClass;
        if (array_key_exists(BuilderiusSavedCompositeModule::LABEL_FIELD, $args)) {
            foreach ($this->modulesProvider->getModules('template', $args['technology'], false) as $module) {
                if ($module->getLabel() === $args[BuilderiusSavedCompositeModule::LABEL_FIELD]) {
                    throw new \Exception('Module with same label already exists.', 400);
                }
            }
            foreach ($this->compositeModulesProvider->getModules('template', $args['technology'], false) as $module) {
                if ($module->getLabel() === $args[BuilderiusSavedCompositeModule::LABEL_FIELD]) {
                    if (!$module instanceof BuilderiusSavedCompositeModule) {
                        throw new \Exception('Module with same label already exists.', 400);
                    } else {
                        if (!isset($args['replace']) || $args['replace'] === false) {
                            throw new \Exception('Module with same label already exists.', 400);
                        } else {
                            $prepared_post->ID = $module->getId();
                        }
                    }
                }
            }

            $prepared_post->post_title = $args[BuilderiusSavedCompositeModule::LABEL_FIELD];
            $prepared_post->post_name = str_replace(' ', '', $args[BuilderiusSavedCompositeModule::LABEL_FIELD]);
        }
        //Icon
        if (array_key_exists(BuilderiusSavedCompositeModule::ICON_FIELD, $args)) {
            $prepared_post->{BuilderiusSavedCompositeModule::ICON_FIELD} = $args[BuilderiusSavedCompositeModule::ICON_FIELD];
        }
        //Category
        if (array_key_exists(BuilderiusSavedCompositeModule::CATEGORY_FIELD, $args)) {
            $prepared_post->{BuilderiusSavedCompositeModule::CATEGORY_FIELD} = $args[BuilderiusSavedCompositeModule::CATEGORY_FIELD];
        }
        //Tags
        if (array_key_exists(BuilderiusSavedCompositeModule::TAGS_FIELD, $args)) {
            $prepared_post->{BuilderiusSavedCompositeModule::TAGS_FIELD} = $args[BuilderiusSavedCompositeModule::TAGS_FIELD];
        }
        //Config
        if (array_key_exists('serialized_config', $args)) {
            $config = json_decode($args['serialized_config'], true);
            if ($config !== null) {
                $config['template']['type'] = 'template';
                $config['template']['technology'] = $args['technology'];
                try {
                    $this->configChecker->check($config);
                } catch (\Exception $e) {
                    throw new \Exception(sprintf('Config is not valid. %s', $e->getMessage()), 400);
                }
            } else {
                throw new \Exception('Config is not valid.', 400);
            }
            $event = new ConfigContainingEvent($config);
            $this->eventDispatcher->dispatch($event, 'builderius_saved_composite_module_config_before_save');
            $config = $event->getConfig();
            //unset($config['template']);
            $prepared_post->{BuilderiusSavedCompositeModule::CONFIG_FIELD} = $config ? json_encode($config, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE) : null;
        }
        // Technology.
        if (isset($args['technology'])) {
            $prepared_post->technology = $args['technology'];
        } else {
            throw new \Exception('Missing required parameter "technology".', 400);
        }
        // Tags.
        if (isset($args['tags'])) {
            $prepared_post->tags = json_encode($args['tags'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
        } else {
            $prepared_post->tags = json_encode([], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
        }
        $prepared_post->post_type = BuilderiusSavedCompositeModulePostType::POST_TYPE;

        // Post status.
        $prepared_post->post_status = 'draft';

        // Author.
        if (!empty($args['author_id'])) {
            $post_author = (int)$args['author_id'];

            if (apply_filters('builderius_get_current_user', wp_get_current_user())->ID !== $post_author) {
                $user_obj = get_userdata($post_author);

                if (!$user_obj) {
                    throw new \Exception('Invalid author ID.', 400);
                }
            }

            $prepared_post->post_author = $post_author;
        } elseif (!property_exists($prepared_post, 'post_author')) {
            $prepared_post->post_author = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
        }

        return $prepared_post;
    }

    /**
     * @param int $post_id
     * @param array $values
     * @param string $taxonomyName
     * @return array|bool|int|int[]|\WP_Error|null
     */
    protected function handleTerms($post_id, array $values, $taxonomyName)
    {
        $result = wp_set_object_terms($post_id, $values, $taxonomyName);

        if (is_wp_error($result)) {
            return $result;
        }

        return null;
    }
}