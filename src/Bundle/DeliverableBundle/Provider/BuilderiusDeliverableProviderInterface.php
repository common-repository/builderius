<?php

namespace Builderius\Bundle\DeliverableBundle\Provider;

use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableInterface;

interface BuilderiusDeliverableProviderInterface
{
    /**
     * @return \WP_Post
     * @throws \Exception
     */
    public function getDeliverablePost();

    /**
     * @return BuilderiusDeliverableInterface
     * @throws \Exception
     */
    public function getDeliverable();
}