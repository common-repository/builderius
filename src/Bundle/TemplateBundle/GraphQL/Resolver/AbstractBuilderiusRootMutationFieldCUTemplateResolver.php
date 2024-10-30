<?php

namespace Builderius\Bundle\TemplateBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\TemplateBundle\ApplyRule\Converter\ApplyRuleConfigConverter;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractBuilderiusRootMutationFieldCUTemplateResolver implements GraphQLFieldResolverInterface
{
    const FORBIDDEN_HOOKS = ['wp_head', 'wp_footer', 'template_include', 'template_redirect'];

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var BuilderiusTemplateFromPostFactory
     */
    protected $templateFactory;

    /**
     * @var BuilderiusTemplateTypesProviderInterface
     */
    protected $templateTypesProvider;

    /**
     * @var \WP_Query
     */
    protected $wpQuery;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusTemplateFromPostFactory $templateFactory
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     * @param \WP_Query $wpQuery
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        BuilderiusTemplateFromPostFactory $templateFactory,
        BuilderiusTemplateTypesProviderInterface $templateTypesProvider,
        \WP_Query $wpQuery
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->templateFactory = $templateFactory;
        $this->templateTypesProvider = $templateTypesProvider;
        $this->wpQuery = $wpQuery;
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
    public function isApplicable($objectValue, array $args, $context, ResolveInfo $info)
    {
        return true;
    }

    /**
     * @param array $args
     * @return \stdClass
     * @throws \Exception
     */
    protected function getPreparedPost(array $args)
    {
        $preparedPost = new \stdClass;

        // Post ID.
        if (isset($args['id'])) {
            $existingPost = get_post((int)$args['id']);
            if (empty($existingPost) || empty($existingPost->ID) ||
                BuilderiusTemplatePostType::POST_TYPE !== $existingPost->post_type) {
                throw new \Exception('Invalid Template ID.', 400);
            }

            $preparedPost = $existingPost;
        }
        // Title
        if (isset($args[BuilderiusTemplate::TITLE_FIELD])) {
            $preparedPost->post_title = $args[BuilderiusTemplate::TITLE_FIELD];
            $preparedPost->post_name = $args[BuilderiusTemplate::TITLE_FIELD];
        } elseif (!$preparedPost->post_title) {
            throw new \Exception('Template title missing.', 400);
        }
        // Name
        if (isset($args[BuilderiusTemplate::NAME_FIELD])) {
            $preparedPost->post_name = $args[BuilderiusTemplate::NAME_FIELD];
        }
        if (!property_exists($preparedPost, 'ID') || !$preparedPost->ID) {
            // Type
            if (!isset($args[BuilderiusTemplate::TYPE_FIELD])) {
                throw new \Exception('Template type missing.', 400);
            } else {
                if (!in_array($args[BuilderiusTemplate::TYPE_FIELD], array_keys($this->templateTypesProvider->getTypes()))) {
                    throw new \Exception('Wrong Template type.', 400);
                } else {
                    $preparedPost->{BuilderiusTemplate::TYPE_FIELD} = $args[BuilderiusTemplate::TYPE_FIELD];
                }
            }
            // Sub Type
            if (!isset($args[BuilderiusTemplate::SUB_TYPE_FIELD])) {
                throw new \Exception('Template sub type missing.', 400);
            } else {
                $preparedPost->{BuilderiusTemplate::SUB_TYPE_FIELD} = $args[BuilderiusTemplate::SUB_TYPE_FIELD];
                if ($args[BuilderiusTemplate::TYPE_FIELD] === 'template' && $args[BuilderiusTemplate::SUB_TYPE_FIELD] === 'hook') {
                    if (!isset($args[BuilderiusTemplate::HOOK_FIELD])) {
                        throw new \Exception('Argument "hook" is required for Hook Template', 400);
                    }
                    if (!isset($args[BuilderiusTemplate::HOOK_TYPE_FIELD])) {
                        throw new \Exception('Argument "hook_type" is required for Hook Template', 400);
                    }
                    if (in_array($args[BuilderiusTemplate::HOOK_FIELD], static::FORBIDDEN_HOOKS)) {
                        throw new \Exception(sprintf('You cannot create Hook Template for hook "%s"', $args[BuilderiusTemplate::HOOK_FIELD]), 400);
                    }
                    $preparedPost->{BuilderiusTemplate::HOOK_FIELD} = $args[BuilderiusTemplate::HOOK_FIELD];
                    $preparedPost->{BuilderiusTemplate::HOOK_TYPE_FIELD} = $args[BuilderiusTemplate::HOOK_TYPE_FIELD];
                    $preparedPost->{BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD} = isset($args[BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD]) ? $args[BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD] : 1;
                    $preparedPost->{BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD} = isset($args[BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD]) && true === $args[BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD] ? 'true' : 'false';
                }
            }
            // Technology
            if (!isset($args[BuilderiusTemplate::TECHNOLOGY_FIELD])) {
                throw new \Exception('Template technology missing.', 400);
            } else {
                $type = $this->templateTypesProvider->getType($args[BuilderiusTemplate::TYPE_FIELD]);
                if (!in_array($args[BuilderiusTemplate::TECHNOLOGY_FIELD], array_keys($type->getTechnologies()))) {
                    throw new \Exception('Wrong Template technology.', 400);
                } else {
                    $preparedPost->{BuilderiusTemplate::TECHNOLOGY_FIELD} = $args[BuilderiusTemplate::TECHNOLOGY_FIELD];
                }
            }
        }
        $preparedPost->post_type = BuilderiusTemplatePostType::POST_TYPE;

        // Post status.
        $preparedPost->post_status = 'publish';

        // Sort Order.
        if (isset($args[BuilderiusTemplate::SORT_ORDER_FIELD])) {
            $preparedPost->{BuilderiusTemplate::SORT_ORDER_FIELD} = $args[BuilderiusTemplate::SORT_ORDER_FIELD];
        } elseif (!property_exists($preparedPost, 'ID') || !$preparedPost->ID) {
            $preparedPost->{BuilderiusTemplate::SORT_ORDER_FIELD} = 10;
        }

        // Apply rule config.
        if (array_key_exists(BuilderiusTemplate::SERIALIZED_APPLY_RULES_CONFIG_GRAPHQL, $args)) {
            if (!empty($args[BuilderiusTemplate::SERIALIZED_APPLY_RULES_CONFIG_GRAPHQL])) {
                try {
                    $applyRulesConfig = json_decode($args[BuilderiusTemplate::SERIALIZED_APPLY_RULES_CONFIG_GRAPHQL], true);
                    foreach ($applyRulesConfig['categories'] as $configSet) {
                        ApplyRuleConfigConverter::convert($configSet);
                    }
                } catch (\Exception $e) {
                    throw new \Exception(sprintf('Apply Rules Config is not correct.%s', $e->getMessage()), 400);
                }
                $event = new ConfigContainingEvent($applyRulesConfig);
                $this->eventDispatcher->dispatch($event, 'builderius_template_apply_rules_config_before_save');
                if (property_exists($preparedPost, BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD)) {
                    $savedConfig = json_decode($preparedPost->{BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD}, true);
                    if (!isset($savedConfig['version'])) {
                        $preparedPost->{BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD} =
                            json_encode($event->getConfig(), JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
                    } else {
                        $newConfig = $event->getConfig();
                        unset($newConfig['version']);
                        unset($savedConfig['version']);
                        if ($savedConfig !== $newConfig) {
                            $preparedPost->{BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD} =
                                json_encode($event->getConfig(), JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
                        }
                    }
                } else {
                    $preparedPost->{BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD} =
                        json_encode($event->getConfig(), JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
                }
            } else {
                $preparedPost->{BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD} = null;
            }
        }
        // Active branch.
        if (!empty($args[BuilderiusTemplate::ACTIVE_BRANCH_NAME_GRAPHQL])) {
            $preparedPost->{BuilderiusTemplate::ACTIVE_BRANCH_NAME_FIELD} =
                $args[BuilderiusTemplate::ACTIVE_BRANCH_NAME_GRAPHQL];
        }
        // Author.
        if (!empty($args['author_id'])) {
            $postAuthorId = $args['author_id'];

            if (apply_filters('builderius_get_current_user', wp_get_current_user())->ID !== $postAuthorId) {
                $user_obj = get_userdata($postAuthorId);

                if (!$user_obj) {
                    throw new \Exception('Invalid author ID.', 400);
                }
            }

            $preparedPost->post_author = $postAuthorId;
        } elseif (!property_exists($preparedPost, 'post_author') || !$preparedPost->post_author) {
            $preparedPost->post_author = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
        }

        return $preparedPost;
    }

    /**
     * @param int $post_id
     * @param string $value
     * @param string $taxonomyName
     * @return array|bool|int|int[]|\WP_Error|null
     */
    protected function handleTerms($post_id, $value, $taxonomyName)
    {
        $result = wp_set_object_terms($post_id, $value, $taxonomyName);

        if (is_wp_error($result)) {
            return $result;
        }

        return null;
    }
}