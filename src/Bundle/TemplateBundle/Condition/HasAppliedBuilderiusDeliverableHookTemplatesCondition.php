<?php

namespace Builderius\Bundle\TemplateBundle\Condition;

use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModulesProviderInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\AbstractCondition;

class HasAppliedBuilderiusDeliverableHookTemplatesCondition extends AbstractCondition
{
    /**
     * @var DeliverableTemplateSubModulesProviderInterface
     */
    private $builderiusDhtsmsProvider;

    /**
     * @param DeliverableTemplateSubModulesProviderInterface $builderiusDhtsmsProvider
     * @return $this
     */
    public function setBuilderiusDeliverableTemplateSubModulesProvider(
        DeliverableTemplateSubModulesProviderInterface $builderiusDhtsmsProvider
    ) {
        $this->builderiusDhtsmsProvider = $builderiusDhtsmsProvider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getResult()
    {
        if ($this->builderiusDhtsmsProvider->getTemplateSubModules()) {
            return true;
        }

        return false;
    }
}