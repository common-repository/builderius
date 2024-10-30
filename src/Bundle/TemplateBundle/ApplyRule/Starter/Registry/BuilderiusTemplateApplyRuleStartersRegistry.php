<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Starter\Registry;

use Builderius\Bundle\TemplateBundle\ApplyRule\Starter\BuilderiusTemplateApplyRuleStarterInterface;

class BuilderiusTemplateApplyRuleStartersRegistry implements
    BuilderiusTemplateApplyRuleStartersRegistryInterface
{
    /**
     * @var BuilderiusTemplateApplyRuleStarterInterface[]
     */
    private $starters = [];

    /**
     * @param BuilderiusTemplateApplyRuleStarterInterface $starter
     * @return $this
     */
    public function addStarter(BuilderiusTemplateApplyRuleStarterInterface $starter)
    {
        $this->starters[] = $starter;
        
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStarters()
    {
        return $this->starters;
    }
}
