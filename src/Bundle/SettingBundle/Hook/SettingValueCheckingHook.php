<?php

namespace Builderius\Bundle\SettingBundle\Hook;

use Builderius\Bundle\SettingBundle\Checker\SettingValue\BuilderiusSettingValueCheckerInterface;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;

class SettingValueCheckingHook extends AbstractFilter
{
    /**
     * @var BuilderiusSettingValueCheckerInterface
     */
    private $settingValueChecker;

    /**
     * @param BuilderiusSettingValueCheckerInterface $settingValueChecker
     */
    public function setSettingValueChecker(BuilderiusSettingValueCheckerInterface $settingValueChecker)
    {
        $this->settingValueChecker = $settingValueChecker;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $arguments = func_get_args();
        $this->settingValueChecker->check($arguments[0], $arguments[1]);
    }
}
