<?php

namespace Builderius\Bundle\ReleaseBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\ReleaseBundle\Factory\BuilderiusReleaseFromPostFactory;
use Builderius\Bundle\ReleaseBundle\Registration\BulderiusReleasePostType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusRootQueryFieldReleasesResolver implements GraphQLFieldResolverInterface
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
        return 'releases';
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
        $queryArgs = [
            'post_type' => BulderiusReleasePostType::POST_TYPE,
            'post_status' => get_post_stati(),
            'posts_per_page' => -1,
            'no_found_rows' => true,
        ];
        if (isset($args['status'])) {
            $queryArgs['post_status'] = $args['status'] === 'publish' ? ['publish', 'future'] : $args['status'];
        }
        $posts = $this->wpQuery->query($queryArgs);
        $releases = [];
        foreach ($posts as $post) {
            $releases[] = $this->releaseFromPostFactory->createRelease($post);
        }

        return $releases;
    }
}