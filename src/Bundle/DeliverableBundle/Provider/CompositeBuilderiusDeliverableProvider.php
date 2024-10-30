<?php

namespace Builderius\Bundle\DeliverableBundle\Provider;

class CompositeBuilderiusDeliverableProvider implements BuilderiusDeliverableProviderInterface
{
    /**
     * @var BuilderiusDeliverableProviderInterface[]
     */
    private $providers = [];

    /**
     * @param BuilderiusDeliverableProviderInterface $provider
     * @return $this
     */
    public function addProvider(BuilderiusDeliverableProviderInterface $provider)
    {
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeliverablePost()
    {
        foreach ($this->providers as $provider) {
            if ($deliverablePost = $provider->getDeliverablePost()) {
                return $deliverablePost;
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getDeliverable()
    {
        foreach ($this->providers as $provider) {
            if ($deliverable = $provider->getDeliverable()) {
                return $deliverable;
            }
        }

        return null;
    }
}