<?php

namespace Builderius\Bundle\SettingBundle\Model;

class BuilderiusCssSettingDecorator extends BuilderiusSettingDecorator
    implements BuilderiusSettingCssAwareInterface
{
    /**
     * {@inheritDoc}
     */
    public function __construct(BuilderiusSettingInterface $setting)
    {
        if (!$setting instanceof BuilderiusSettingCssAwareInterface) {
            throw new \Exception(
                sprintf(
                    '%s can decorate only classes which implements %s',
                    self::class,
                    BuilderiusSettingCssAwareInterface::class
                )
            );
        }
        if ($setting instanceof BuilderiusModuleSettingInterface) {
            throw new \Exception(
                sprintf(
                    '%s can\'t decorate classes which implements %s',
                    self::class,
                    BuilderiusModuleSettingInterface::class
                )
            );
        }
        $this->setting = $setting;
    }

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
        return $this->setting->setAtRules($atRules);
    }

    /**
     * @inheritDoc
     */
    public function addAtRule(BuilderiusSettingCssAtRuleInterface $atRule)
    {
        return $this->setting->addAtRule($atRule);
    }

    /**
     * @inheritDoc
     */
    public function getAtRules()
    {
        return $this->setting->getAtRules();
    }

    /**
     * @inheritDoc
     */
    public function hasAtRules()
    {
        return $this->setting->hasAtRules();
    }
}