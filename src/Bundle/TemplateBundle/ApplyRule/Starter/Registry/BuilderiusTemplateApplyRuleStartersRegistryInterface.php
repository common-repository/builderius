<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Starter\Registry;

use Builderius\Bundle\TemplateBundle\ApplyRule\Starter\BuilderiusTemplateApplyRuleStarterInterface;

interface BuilderiusTemplateApplyRuleStartersRegistryInterface
{
    /**
     * @return BuilderiusTemplateApplyRuleStarterInterface[]
     */
    public function getStarters();
}
