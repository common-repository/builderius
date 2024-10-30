<?php

namespace Builderius\Bundle\ReleaseBundle\Provider;

use Builderius\Bundle\DeliverableBundle\Provider\BuilderiusDeliverableProviderInterface;
use Builderius\Bundle\ReleaseBundle\Factory\BuilderiusReleaseFromPostFactory;
use Builderius\Bundle\ReleaseBundle\Registration\BulderiusReleasePostType;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;

class BuilderiusDeliverableReleaseProvider implements BuilderiusDeliverableProviderInterface
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;
    /**
     * @var BuilderiusReleaseFromPostFactory
     */
    private $builderiusReleaseFromPostFactory;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param \WP_Query $wpQuery
     * @param BuilderiusReleaseFromPostFactory $builderiusReleaseFromPostFactory
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        \WP_Query $wpQuery,
        BuilderiusReleaseFromPostFactory $builderiusReleaseFromPostFactory,
        BuilderiusRuntimeObjectCache $cache
    )
    {
        $this->wpQuery = $wpQuery;
        $this->builderiusReleaseFromPostFactory = $builderiusReleaseFromPostFactory;
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function getDeliverablePost()
    {
        $releasePost = $this->cache->get('builderius_release_post');
        if (false === $releasePost) {
            $releasePost = null;
            $releasePosts = $this->wpQuery->query([
                'post_type' => BulderiusReleasePostType::POST_TYPE,
                'post_status' => ['publish', 'future'],
                'posts_per_page' => -1,
                'no_found_rows' => true,
            ]);
            if (!empty($releasePosts)) {
                $releasePost = reset($releasePosts);
                $this->cache->set('builderius_release_post', $releasePost);
            } else {
                $this->cache->set('builderius_release_post', null);
            }
        }

        return $releasePost;
    }

    /**
     * @inheritDoc
     */
    public function getDeliverable()
    {
        $release = $this->cache->get('builderius_release');
        if (false === $release) {
            $release = null;
            $releasePost = $this->getDeliverablePost();
            if ($releasePost) {
                $release = $this->builderiusReleaseFromPostFactory->createRelease($releasePost);
                $this->cache->set('builderius_release', $release);
            } else {
                $this->cache->set('builderius_release', null);
            }
        }

        return $release;
    }
}