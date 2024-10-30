<?php

namespace Builderius\Bundle\VCSBundle\EventListener;

use Builderius\Bundle\VCSBundle\Event\BuilderiusBranchFromPostCreationEvent;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusBranchFromPostFactory;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranchInterface;

class BuilderiusBranchFromPostCreationEventListener
{
    /**
     * @var BuilderiusBranchFromPostFactory
     */
    private $branchFromPostFactory;

    /**
     * @param BuilderiusBranchFromPostFactory $branchFromPostFactory
     */
    public function __construct(BuilderiusBranchFromPostFactory $branchFromPostFactory)
    {
        $this->branchFromPostFactory = $branchFromPostFactory;
    }

    public function createBranch(BuilderiusBranchFromPostCreationEvent $event)
    {
        $post = $event->getPost();
        if ($post instanceof \WP_Post) {
            $branch = $this->branchFromPostFactory->createBranch($post);
            if ($branch instanceof BuilderiusBranchInterface) {
                $event->setBranch($branch);
            }
        }
    }
}