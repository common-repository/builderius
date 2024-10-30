<?php

namespace Builderius\Bundle\DeliverableBundle\Event;

use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableInterface;
use Builderius\Symfony\Contracts\EventDispatcher\Event;

class DeliverableContainingEvent extends Event
{
    /**
     * @var BuilderiusDeliverableInterface
     */
    private $deliverable;

    /**
     * @param BuilderiusDeliverableInterface $deliverable
     */
    public function __construct(BuilderiusDeliverableInterface $deliverable)
    {
        $this->deliverable = $deliverable;
    }

    /**
     * @return BuilderiusDeliverableInterface
     */
    public function getDeliverable()
    {
        return $this->deliverable;
    }

    /**
     * @param BuilderiusDeliverableInterface $deliverable
     * @return $this
     */
    public function setDeliverable(BuilderiusDeliverableInterface $deliverable)
    {
        $this->deliverable = $deliverable;

        return $this;
    }
}