<?php

namespace Builderius\Bundle\DeliverableBundle\Condition;

use Builderius\Bundle\DeliverableBundle\Provider\BuilderiusDeliverableProviderInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\AbstractCondition;

class HasAppliedBuilderiusDeliverableCondition extends AbstractCondition
{
    /**
     * @var BuilderiusDeliverableProviderInterface
     */
    private $builderiusDeliverableProvider;

    /**
     * @param BuilderiusDeliverableProviderInterface $builderiusDeliverableProvider
     * @return $this
     */
    public function setBuilderiusDeliverableProvider(BuilderiusDeliverableProviderInterface $builderiusDeliverableProvider)
    {
        $this->builderiusDeliverableProvider = $builderiusDeliverableProvider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getResult()
    {
        if ($this->builderiusDeliverableProvider->getDeliverablePost()) {
            return true;
        }

        return false;
    }
}