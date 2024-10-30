<?php

namespace Builderius\Bundle\TemplateBundle\Condition;

use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\AbstractCondition;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;

class HasAppliedBuilderiusTemplateCondition extends AbstractCondition
{
    /**
     * @var BuilderiusTemplateProviderInterface
     */
    private $builderiusTemplateProvider;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param BuilderiusTemplateProviderInterface $builderiusTemplateProvider
     * @return HasAppliedBuilderiusTemplateCondition
     */
    public function setBuilderiusTemplateProvider(BuilderiusTemplateProviderInterface $builderiusTemplateProvider)
    {
        $this->builderiusTemplateProvider = $builderiusTemplateProvider;

        return $this;
    }

    /**
     * @param EventDispatcher $eventDispatcher
     * @return $this
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getResult()
    {
        if ($this->builderiusTemplateProvider->getTemplatePost()) {
            return true;
        }

        return false;
    }
}