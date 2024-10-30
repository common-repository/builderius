<?php

namespace Builderius\Bundle\SavedFragmentBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Bundle\SavedFragmentBundle\Factory\BuilderiusSavedFragmentFromPostFactory;
use Builderius\Bundle\SavedFragmentBundle\Model\BuilderiusSavedFragment;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentPostType;

class BuilderiusRootQueryFieldSavedFragmentResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @param \WP_Query $wpQuery
     */
    public function __construct(\WP_Query $wpQuery)
    {
        $this->wpQuery = $wpQuery;
    }

    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['BuilderiusRootQuery'];
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'saved_fragment';
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
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $posts = $this->wpQuery->query([
            'p' => $args[BuilderiusSavedFragment::ID_FIELD],
            'post_type' => BuilderiusSavedFragmentPostType::POST_TYPE,
            'posts_per_page' => 1,
            'no_found_rows' => true,
            'post_status' => get_post_stati(),
        ]);
        if (empty($posts)) {
            throw new \Exception('There is no Builderius Saved Fragment with provided ID', 400);
        }
        $post = reset($posts);

        return BuilderiusSavedFragmentFromPostFactory::createSavedFragment($post);
    }
}