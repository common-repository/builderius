<?php

namespace Builderius\Bundle\ReleaseBundle\GraphQL\Resolver;

use Builderius\Bundle\DeliverableBundle\Event\DeliverableContainingEvent;
use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\ReleaseBundle\Factory\BuilderiusReleaseFromPostFactory;
use Builderius\Bundle\ReleaseBundle\Registration\BulderiusReleasePostType;
use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldExtractReleaseResolver implements GraphQLFieldResolverInterface
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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param \WP_Query $wpQuery
     * @param BuilderiusReleaseFromPostFactory $releaseFromPostFactory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        \WP_Query $wpQuery,
        BuilderiusReleaseFromPostFactory $releaseFromPostFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->wpQuery = $wpQuery;
        $this->releaseFromPostFactory = $releaseFromPostFactory;
        $this->eventDispatcher = $eventDispatcher;
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
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'extractRelease';
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        $postToBeExtracted = get_post((int)$args['id']);
        if (empty($postToBeExtracted) || empty($postToBeExtracted->ID) ||
            BulderiusReleasePostType::POST_TYPE !== $postToBeExtracted->post_type) {
            throw new \Exception('Invalid Release ID.', 400);
        }
        $this->eventDispatcher->dispatch(new PostContainingEvent($postToBeExtracted), 'builderius_release_before_extraction');
        $this->eventDispatcher->dispatch(new PostContainingEvent($postToBeExtracted), 'builderius_deliverable_before_extraction');

        $release = $this->releaseFromPostFactory->createRelease($postToBeExtracted);
        $this->eventDispatcher->dispatch(new DeliverableContainingEvent($release), 'builderius_release_extraction');
        $this->eventDispatcher->dispatch(new DeliverableContainingEvent($release), 'builderius_deliverable_extraction');

        $this->eventDispatcher->dispatch(new PostContainingEvent($postToBeExtracted), 'builderius_release_extracted');
        $this->eventDispatcher->dispatch(new PostContainingEvent($postToBeExtracted), 'builderius_deliverable_extracted');

        return ['result' => true, 'message' => 'Release was extracted successfully.'];
    }
}