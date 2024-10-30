<?php

namespace Builderius\Bundle\ReleaseBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\ReleaseBundle\Factory\BuilderiusReleaseFromPostFactory;
use Builderius\Bundle\ReleaseBundle\Model\BuilderiusRelease;
use Builderius\Bundle\ReleaseBundle\Registration\BulderiusReleasePostType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusRootQueryFieldReleaseResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusReleaseFromPostFactory
     */
    private $releaseFromPostFactory;

    /**
     * @param \WP_Query $wpQuery
     * @param BuilderiusReleaseFromPostFactory $releaseFromPostFactory
     */
    public function __construct(\WP_Query $wpQuery, BuilderiusReleaseFromPostFactory $releaseFromPostFactory)
    {
        $this->wpQuery = $wpQuery;
        $this->releaseFromPostFactory = $releaseFromPostFactory;
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
        return 'release';
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
            'p' => $args[BuilderiusRelease::ID_FIELD],
            'post_type' => BulderiusReleasePostType::POST_TYPE,
            'post_status' => get_post_stati(),
            'posts_per_page' => -1,
            'no_found_rows' => true,
        ]);
        if (empty($posts)) {
            throw new \Exception('There is no Builderius Release with provided ID', 400);
        }
        $post = reset($posts);

        return $this->releaseFromPostFactory->createRelease($post);
    }
}