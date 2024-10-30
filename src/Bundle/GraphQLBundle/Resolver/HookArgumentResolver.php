<?php

namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\GraphQL\Cache\GraphQLObjectCache;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class HookArgumentResolver extends AbstractLocalVarsAwareResolver
{
    /**
     * @var array
     */
    protected $typeNames;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param array $typeNames
     * @param GraphQLObjectCache $graphqlSubfieldsCache
     * @param ExpressionLanguage $expressionLanguage
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        array $typeNames,
        GraphQLObjectCache $graphqlSubfieldsCache,
        ExpressionLanguage $expressionLanguage,
        BuilderiusRuntimeObjectCache $cache
    ) {
        $this->typeNames = $typeNames;
        $this->cache = $cache;
        parent::__construct($graphqlSubfieldsCache, $expressionLanguage);
    }

    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return $this->typeNames;
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
        return 'hook_argument';
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
        if (!isset($args['position'])) {
            return null;
        }
        $args = $this->processArguments($args, $info->path);
        $template = $this->cache->get('builderius_hook_template');
        if ($template) {
            $index = $this->cache->get(sprintf('hook_argument_resolver_%d_%d_index', $template->getId(), $args['position']));
            if (false === $index) {
                $index = 0;
            } else {
                $index = $index + 1;
            }
            $cachedHookArgs = $this->cache->get(sprintf('hook_template_args_%d_%d', $template->getId(), $index));
            if (false !== $cachedHookArgs) {
                $this->cache->set(sprintf('hook_argument_resolver_%d_%d_index', $template->getId(), $args['position']), $index);
            }
        } elseif ($templateSm = $this->cache->get('builderius_dtsm_hook_template')) {
            $index = $this->cache->get(sprintf('dtsm_hook_argument_resolver_%d_%d_index', $templateSm->getId(), $args['position']));
            if (false === $index) {
                $index = 0;
            } else {
                $index = $index + 1;
            }
            $cachedHookArgs = $this->cache->get(sprintf('dtsm_hook_template_args_%d_%d', $templateSm->getId(), $index));
            if (false !== $cachedHookArgs) {
                $this->cache->set(sprintf('dtsm_hook_argument_resolver_%d_%d_index', $templateSm->getId(), $args['position']), $index);
            }
        } else {
            $cachedHookArgs = $this->cache->get('hook_template_args');
        }
        if (false === $cachedHookArgs) {
            $this->cache->set('getting_hook_args_before_hook', true);
        }
        if (!is_array($cachedHookArgs) || !isset($cachedHookArgs[$args['position']])) {
            return null;
        }
        if (false !== $cachedHookArgs) {
            $this->cache->delete('getting_hook_args_before_hook');
        }

        return $cachedHookArgs[$args['position']];
    }
}