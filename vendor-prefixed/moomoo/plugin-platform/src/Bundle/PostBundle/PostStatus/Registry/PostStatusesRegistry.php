<?php

namespace Builderius\MooMoo\Platform\Bundle\PostBundle\PostStatus\Registry;

use Builderius\MooMoo\Platform\Bundle\PostBundle\PostStatus\PostStatusInterface;
class PostStatusesRegistry implements \Builderius\MooMoo\Platform\Bundle\PostBundle\PostStatus\Registry\PostStatusesRegistryInterface
{
    /**
     * @var PostStatusInterface[]
     */
    private $postStatuses = [];
    /**
     * @param PostStatusInterface $postStatus
     */
    public function addPostStatus(\Builderius\MooMoo\Platform\Bundle\PostBundle\PostStatus\PostStatusInterface $postStatus)
    {
        $this->postStatuses[$postStatus->getStatus()] = $postStatus;
    }
    /**
     * @inheritDoc
     */
    public function getPostStatuses()
    {
        return $this->postStatuses;
    }
    /**
     * @inheritDoc
     */
    public function getPostStatus($Status)
    {
        if ($this->hasPostStatus($Status)) {
            return $this->postStatuses[$Status];
        }
        return null;
    }
    /**
     * @inheritDoc
     */
    public function hasPostStatus($Status)
    {
        return isset($this->postStatuses[$Status]);
    }
}
