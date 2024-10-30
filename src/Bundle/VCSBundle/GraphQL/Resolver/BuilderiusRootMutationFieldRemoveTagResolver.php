<?php

namespace Builderius\Bundle\VCSBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\VCSBundle\Event\BuilderiusVCSTagEvent;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusCommitFromPostFactory;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusVCSTagTaxonomy;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusRootMutationFieldRemoveTagResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var BuilderiusCommitFromPostFactory
     */
    private $commitFactory;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param BuilderiusCommitFromPostFactory $commitFactory
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        BuilderiusCommitFromPostFactory $commitFactory
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->commitFactory = $commitFactory;
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
    public function getFieldName()
    {
        return 'removeTag';
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
        $input = $args['input'];
        $commitPost = get_post((int)$input['commit_id']);
        if (!$commitPost || $commitPost->post_type !== BuilderiusCommitPostType::POST_TYPE) {
            throw new \Exception('No Commit with provided commit_id.', 400);
        }
        $event = new BuilderiusVCSTagEvent(
            $this->commitFactory->createCommit($commitPost),
            $input['tag']
        );
        $this->eventDispatcher->dispatch(
            $event,
            'builderius_commit_before_tag_removal'
        );

        wp_remove_object_terms($commitPost->ID, [$input['tag']], BuilderiusVCSTagTaxonomy::NAME);
        $event = new BuilderiusVCSTagEvent(
            $this->commitFactory->createCommit($commitPost),
            $input['tag']
        );
        $this->eventDispatcher->dispatch(
            $event,
            'builderius_commit_tag_removed'
        );

        $commit = $this->commitFactory->createCommit($commitPost);

        return new \ArrayObject(['commit' => $commit]);
    }
}