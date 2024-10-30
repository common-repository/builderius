<?php

namespace Builderius\Bundle\TemplateBundle\Condition;

use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModuleProviderInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\AbstractCondition;

class HasAppliedBuilderiusDeliverableTemplateCondition extends AbstractCondition
{
    /**
     * @var DeliverableTemplateSubModuleProviderInterface
     */
    private $builderiusDtsmProvider;

    /**
     * @param DeliverableTemplateSubModuleProviderInterface $builderiusDtsmProvider
     * @return $this
     */
    public function setBuilderiusDeliverableTemplateSubModuleProvider(
        DeliverableTemplateSubModuleProviderInterface $builderiusDtsmProvider
    ) {
        $this->builderiusDtsmProvider = $builderiusDtsmProvider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getResult()
    {
        if ($this->builderiusDtsmProvider->getTemplateSubModule()) {
            return true;
        }

        return false;
    }
}