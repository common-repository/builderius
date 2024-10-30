<?php

namespace Builderius\Bundle\TemplateBundle\Condition;

use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplatesProviderInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\AbstractCondition;

class HasAppliedBuilderiusHookTemplatesCondition extends AbstractCondition
{
    /**
     * @var BuilderiusTemplatesProviderInterface
     */
    private $builderiusTemplatesProvider;

    /**
     * @param BuilderiusTemplatesProviderInterface $builderiusTemplatesProvider
     * @return $this
     */
    public function setBuilderiusHookTemplatesProvider(BuilderiusTemplatesProviderInterface $builderiusTemplatesProvider)
    {
        $this->builderiusTemplatesProvider = $builderiusTemplatesProvider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getResult()
    {
        if (!empty($this->builderiusTemplatesProvider->getTemplatePosts())) {
            return true;
        }

        return false;
    }
}