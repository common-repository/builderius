<?php

namespace Builderius\Bundle\DeliverableBundle\Factory\BuilderiusDeliverable\Chain\Element;

use Builderius\Bundle\DeliverableBundle\Factory\BuilderiusDeliverable\BuilderiusDeliverableFromPostFactoryInterface;

abstract class AbstractBuilderiusDeliverableFromPostFactoryChainElement implements
    BuilderiusDeliverableFromPostFactoryInterface,
    BuilderiusDeliverableFromPostFactoryChainElementInterface
{
    /**
     * @var BuilderiusDeliverableFromPostFactoryChainElementInterface|null
     */
    private $successor;

    /**
     * @inheritDoc
     */
    public function getSuccessor()
    {
        return $this->successor;
    }

    /**
     * @inheritDoc
     */
    public function setSuccessor(BuilderiusDeliverableFromPostFactoryChainElementInterface $successor)
    {
        $this->successor = $successor;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function createDeliverable(\WP_Post $post)
    {
        if ($this->isApplicable($post)) {
            return $this->create($post);
        } elseif ($this->getSuccessor()) {
            return $this->getSuccessor()->createDeliverable($post);
        }

        return null;
    }
}