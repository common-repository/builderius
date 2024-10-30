<?php

namespace Builderius\Bundle\SettingBundle\Model;

class BuilderiusModuleCssSetting extends BuilderiusModuleSetting implements BuilderiusSettingCssAwareInterface
{
    /**
     * @inheritDoc
     */
    public function getContentType()
    {
        return parent::getContentType() ? : 'css';
    }

    /**
     * @inheritDoc
     */
    public function setAtRules(array $atRules)
    {
        $this->set(self::AT_RULES_FIELD, $atRules);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addAtRule(BuilderiusSettingCssAtRuleInterface $atRule)
    {
        $atRules = $this->getAtRules();
        $atRules[] = $atRule;
        $this->setAtRules($atRules);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAtRules()
    {
        return $this->get(self::AT_RULES_FIELD, []);
    }
    
    /**
     * @inheritDoc
     */
    public function hasAtRules()
    {
        return !empty($this->getAtRules());
    }
}
