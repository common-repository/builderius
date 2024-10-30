<?php

namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class PostContentResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param BuilderiusRuntimeObjectCache $cache
     * @return $this
     */
    public function setCache(BuilderiusRuntimeObjectCache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['Post'];
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
        return 'post_content';
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
        if (isset($args['raw']) && $args['raw'] === true) {
            return $objectValue->post_content;
        } else {
            /** @var \WP_Post $objectValue */
            $GLOBALS['post'] = $objectValue;
            $postContent = $this->cache->get(sprintf('builderius_graphql_post_%d_content', $objectValue->ID));
            if (false === $postContent) {
                $this->cache->set('builderius_graphql_post_content_resolving', true);
                $postContent = str_replace(
                    ']]>', ']]&gt;',
                    apply_filters(
                        'the_content',
                        get_the_content(
                            null,
                            false,
                            $objectValue
                        )
                    )
                );
                $this->cache->set(sprintf('builderius_graphql_post_%d_content', $objectValue->ID), $postContent);
                $this->cache->delete('builderius_graphql_post_content_resolving');
            }

            return $postContent;
        }
    }
}