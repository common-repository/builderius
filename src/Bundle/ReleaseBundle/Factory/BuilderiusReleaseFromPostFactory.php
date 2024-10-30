<?php

namespace Builderius\Bundle\ReleaseBundle\Factory;

use Builderius\Bundle\DeliverableBundle\Factory\BuilderiusDeliverableSubModuleFromPostFactory;
use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableSubModuleInterface;
use Builderius\Bundle\DeliverableBundle\Registration\BulderiusDeliverableSubModulePostType;
use Builderius\Bundle\ReleaseBundle\Model\BuilderiusRelease;
use Builderius\Bundle\ReleaseBundle\Model\BuilderiusReleaseInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;

class BuilderiusReleaseFromPostFactory
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusDeliverableSubModuleFromPostFactory
     */
    private $dsmFromPostFactory;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param \WP_Query $wpQuery
     * @param BuilderiusDeliverableSubModuleFromPostFactory $dsmFromPostFactory
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        \WP_Query $wpQuery,
        BuilderiusDeliverableSubModuleFromPostFactory $dsmFromPostFactory,
        BuilderiusRuntimeObjectCache $cache
    ) {
        $this->wpQuery = $wpQuery;
        $this->dsmFromPostFactory = $dsmFromPostFactory;
        $this->cache = $cache;
    }

    /**
     * @param \WP_Post $post
     * @return BuilderiusReleaseInterface|null
     */
    public function createRelease(\WP_Post $post)
    {
        return new BuilderiusRelease([
            BuilderiusRelease::ID_FIELD => $post->ID,
            BuilderiusRelease::TAG_FIELD => $post->post_title,
            BuilderiusRelease::DESCRIPTION_FIELD => $post->post_excerpt,
            BuilderiusRelease::STATUS_FIELD => $post->post_status === 'future' ? 'publish' : $post->post_status,
            BuilderiusRelease::AUTHOR_FIELD => get_user_by('ID', $post->post_author),
            BuilderiusRelease::CREATED_AT_FIELD => $post->post_date,
            BuilderiusRelease::SUB_MODULES_FIELD => function ($entityType, $type, $technology) use ($post) {
                $subModules = $this->cache->get(sprintf('builderius_release_%s_sub_modules', $post->ID));
                if (false === $subModules) {
                    $subModules = [];
                    $subModulesPosts = $this->cache->get(sprintf('builderius_release_%s_sub_modules_posts', $post->ID));
                    if (false === $subModulesPosts) {
                        $subModulesPosts = $this->wpQuery->query([
                            'post_type' => BulderiusDeliverableSubModulePostType::POST_TYPE,
                            'post_parent' => $post->ID,
                            'post_status' => ['draft'],//get_post_stati(),
                            'posts_per_page' => -1,
                            'no_found_rows' => true,
                            'orderby' => 'ID',
                            'order' => 'DESC'
                        ]);
                        $this->cache->set(sprintf('builderius_release_%s_sub_modules_posts', $post->ID), $subModulesPosts);
                    }
                    /** @var BuilderiusDeliverableSubModuleInterface[] $subModules */
                    foreach ($subModulesPosts as $subModulePost) {
                        if (false === $this->cache->get(sprintf('builderius_dsm_post_%s', $subModulePost->ID))) {
                            $this->cache->set(sprintf('builderius_dsm_post_%s', $subModulePost->ID), $subModulePost);
                        }
                        $subModule = $this->dsmFromPostFactory->createDeliverableSubModule($subModulePost);
                        $this->cache->set(sprintf('builderius_dsm_%s', $subModulePost->ID), $subModule);
                        $subModules[] = $subModule;
                    }
                    $this->cache->set(sprintf('builderius_release_%s_sub_modules', $post->ID), $subModules);
                }
                if ($entityType || $type || $technology) {
                    foreach ($subModules as $k => $subModule) {
                        if ($entityType && $entityType !== $subModule->getEntityType()) {
                            unset($subModules[$k]);
                        }
                        if ($type && $type !== $subModule->getType()) {
                            unset($subModules[$k]);
                        }
                        if ($technology && $technology !== $subModule->getTechnology()) {
                            unset($subModules[$k]);
                        }
                    }
                    sort($subModules);
                }

                return $subModules;
            }
        ]);
    }
}
