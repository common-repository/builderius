<?php

namespace Builderius\Bundle\VCSBundle\Factory\BuilderiusVCSOwner\Chain\Element;

use Builderius\Bundle\VCSBundle\Model\BuilderiusVCSOwnerInterface;

interface BuilderiusVCSOwnerFromPostFactoryChainElementInterface
{
    /**
     * @return BuilderiusVCSOwnerFromPostFactoryChainElementInterface
     */
    public function getSuccessor();

    /**
     * @param BuilderiusVCSOwnerFromPostFactoryChainElementInterface $successor
     * @return $this
     */
    public function setSuccessor(BuilderiusVCSOwnerFromPostFactoryChainElementInterface $successor);

    /**
     * @param \WP_Post $post
     * @return bool
     */
    public function isApplicable(\WP_Post $post);

    /**
     * @param \WP_Post $post
     * @return BuilderiusVCSOwnerInterface
     */
    public function create(\WP_Post $post);
}