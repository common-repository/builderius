<?php

namespace Builderius\Bundle\VCSBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusCommitFromPostFactory;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommit;

class OldCommitsDeletionEventListener
{
    /**
     * @var BuilderiusCommitFromPostFactory
     */
    private $commitFromPostFactory;

    /**
     * @param BuilderiusCommitFromPostFactory $commitFromPostFactory
     */
    public function __construct(BuilderiusCommitFromPostFactory $commitFromPostFactory)
    {
        $this->commitFromPostFactory = $commitFromPostFactory;
    }

    /**
     * @param PostContainingEvent $event
     */
    public function onCommitCreation(PostContainingEvent $event)
    {
        $commitPost = $event->getPost();
        if ($commitPost) {
            $commit = $this->commitFromPostFactory->createCommit($commitPost);
            if ($commit) {
                $branch = $commit->getBranch();
                $commits = $branch->getCommits();
                $allowedQty = $this->getAllowedCommitsQuantity();
                if (count($commits) > $allowedQty) {
                    $i = 0;
                    foreach ($commits as $commit) {
                        ++$i;
                        if ($i > $allowedQty) {
                            wp_delete_post($commit->getId(), true);
                        }
                    }
                }
            }
        }
    }

    private function getAllowedCommitsQuantity()
    {
        $option = get_option('builderius_allowed_commits_qty');

        return $option > 25 ? $option : 25;
    }
}