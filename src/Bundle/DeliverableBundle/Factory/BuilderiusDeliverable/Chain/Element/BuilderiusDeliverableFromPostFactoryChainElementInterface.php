<?php

namespace Builderius\Bundle\DeliverableBundle\Factory\BuilderiusDeliverable\Chain\Element;

use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableInterface;

interface BuilderiusDeliverableFromPostFactoryChainElementInterface
{
    /**
     * @return BuilderiusDeliverableFromPostFactoryChainElementInterface
     */
    public function getSuccessor();

    /**
     * @param BuilderiusDeliverableFromPostFactoryChainElementInterface $successor
     * @return $this
     */
    public function setSuccessor(BuilderiusDeliverableFromPostFactoryChainElementInterface $successor);

    /**
     * @param \WP_Post $post
     * @return bool
     */
    public function isApplicable(\WP_Post $post);

    /**
     * @param \WP_Post $post
     * @return BuilderiusDeliverableInterface
     */
    public function create(\WP_Post $post);
}