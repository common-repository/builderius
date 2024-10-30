<?php

namespace Builderius\Bundle\VCSBundle\Event;

use Builderius\Bundle\VCSBundle\Model\BuilderiusCommitInterface;
use Builderius\Symfony\Contracts\EventDispatcher\Event;

class BuilderiusVCSTagEvent extends Event
{
    /**
     * @var BuilderiusCommitInterface
     */
    private $commit;

    /**
     * @var string
     */
    private $tag;

    /**
     * @param BuilderiusCommitInterface $commit
     * @param string $tag
     */
    public function __construct(
        BuilderiusCommitInterface $commit, string $tag
    ) {
        $this->commit = $commit;
        $this->tag = $tag;
    }

    /**
     * @return BuilderiusCommitInterface
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * @param BuilderiusCommitInterface $commit
     * @return BuilderiusVCSTagEvent
     */
    public function setCommit(BuilderiusCommitInterface $commit)
    {
        $this->commit = $commit;
        return $this;
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     * @return BuilderiusVCSTagEvent
     */
    public function setTag(string $tag)
    {
        $this->tag = $tag;
        return $this;
    }
}