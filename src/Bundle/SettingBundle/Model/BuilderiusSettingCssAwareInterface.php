<?php

namespace Builderius\Bundle\SettingBundle\Model;

interface BuilderiusSettingCssAwareInterface
{
    const CSS_FIELD = 'css';
    const AT_RULES_FIELD = 'atRules';

    /**
     * @param BuilderiusSettingCssAtRuleInterface[] $atRules
     * @return $this
     * @throws \Exception
     */
    public function setAtRules(array $atRules);
    
    /**
     * @param BuilderiusSettingCssAtRuleInterface $atRule
     * @return $this
     * @throws \Exception
     */
    public function addAtRule(BuilderiusSettingCssAtRuleInterface $atRule);

    /**
     * @return BuilderiusSettingCssAtRuleInterface[]
     */
    public function getAtRules();

    /**
     * @return boolean
     */
    public function hasAtRules();
}
