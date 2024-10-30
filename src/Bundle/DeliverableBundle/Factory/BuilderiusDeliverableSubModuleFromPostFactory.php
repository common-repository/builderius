<?php

namespace Builderius\Bundle\DeliverableBundle\Factory;

use Builderius\Bundle\DeliverableBundle\Event\BuilderiusDeliverableFromPostCreationEvent;
use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableInterface;
use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableSubModule;
use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableSubModuleInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Helper\ContentConfigHelper;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusDeliverableSubModuleFromPostFactory
{
    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param BuilderiusRuntimeObjectCache $cache
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        BuilderiusRuntimeObjectCache $cache,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->cache = $cache;
        $this->eventDispatcher = $eventDispatcher;
    }
    
    /**
     * @param \WP_Post $post
     * @return BuilderiusDeliverableSubModuleInterface|null
     */
    public function createDeliverableSubModule(\WP_Post $post)
    {
        $excerpt = json_decode($post->post_excerpt, true);

        return new BuilderiusDeliverableSubModule([
            BuilderiusDeliverableSubModule::ID_FIELD => $post->ID,
            BuilderiusDeliverableSubModule::NAME_FIELD => $post->post_title,
            BuilderiusDeliverableSubModule::ENTITY_TYPE_FIELD => $excerpt[BuilderiusDeliverableSubModule::ENTITY_TYPE_FIELD],
            BuilderiusDeliverableSubModule::TYPE_FIELD => $excerpt[BuilderiusDeliverableSubModule::TYPE_FIELD],
            BuilderiusDeliverableSubModule::TECHNOLOGY_FIELD => $excerpt[BuilderiusDeliverableSubModule::TECHNOLOGY_FIELD],
            BuilderiusDeliverableSubModule::CONTENT_CONFIG_FIELD =>
                ContentConfigHelper::formatConfig(
                    json_decode(
                        get_post_meta(
                            $post->ID,
                            BuilderiusDeliverableSubModule::CONTENT_CONFIG_FIELD,
                            true
                        ),
                        true
                    )
                ),
            BuilderiusDeliverableSubModule::ATTRIBUTES_FIELD =>
                json_decode(
                    get_post_meta(
                        $post->ID,
                        BuilderiusDeliverableSubModule::ATTRIBUTES_FIELD,
                        true
                    ),
                    true
                ),
            BuilderiusDeliverableSubModule::CONTENT_FIELD =>
                json_decode(
                    $post->post_content,
                    true
                ),
            BuilderiusDeliverableSubModule::OWNER_FIELD => function () use ($post) {
                $deliverable = $this->cache->get(sprintf('builderius_deliverable_%s', $post->post_parent));
                if (false === $deliverable) {
                    $deliverable = null;
                    $deliverablePost = $this->cache->get(sprintf('builderius_deliverable_post_%s', $post->post_parent));
                    if (false === $deliverablePost) {
                        $deliverablePost = get_post((int)$post->post_parent);
                        $this->cache->set(sprintf('builderius_deliverable_post_%s', $post->post_parent), $deliverablePost);
                    }
                    if ($deliverablePost) {
                        $event = new BuilderiusDeliverableFromPostCreationEvent($deliverablePost);
                        $this->eventDispatcher->dispatch($event, 'builderius_deliverable_from_post_creation');
                        $deliverable = $event->getDeliverable();
                        if ($deliverable instanceof BuilderiusDeliverableInterface) {
                            $this->cache->set(sprintf('builderius_deliverable_%s', $post->post_parent), $deliverable);
                        }
                    }
                }
                return $deliverable;
            },
        ]);
    }
}
