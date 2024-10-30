<?php

namespace Builderius\Bundle\DeliverableBundle\Factory\BuilderiusDeliverable;

use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableInterface;

interface BuilderiusDeliverableFromPostFactoryInterface
{
    /**
     * @param \WP_Post $post
     * @return BuilderiusDeliverableInterface
     */
    public function createDeliverable(\WP_Post $post);
}