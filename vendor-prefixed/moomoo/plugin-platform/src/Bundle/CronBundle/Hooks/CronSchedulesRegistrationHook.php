<?php

namespace Builderius\MooMoo\Platform\Bundle\CronBundle\Hooks;

use Builderius\MooMoo\Platform\Bundle\CronBundle\Checker\CronSchedule\CronScheduleCheckerInterface;
use Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronSchedule;
use Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronScheduleInterface;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractHook;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\HookInterface;
class CronSchedulesRegistrationHook extends \Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractHook
{
    /**
     * @var CronScheduleInterface[]
     */
    private $schedules = [];
    /**
     * @var CronScheduleCheckerInterface
     */
    private $checker;
    /**
     * @param CronScheduleCheckerInterface $checker
     * @return $this
     */
    public function setChecker(\Builderius\MooMoo\Platform\Bundle\CronBundle\Checker\CronSchedule\CronScheduleCheckerInterface $checker)
    {
        $this->checker = $checker;
        return $this;
    }
    /**
     * @param CronScheduleInterface $schedule
     * @return $this
     */
    public function addCronSchedule(\Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronScheduleInterface $schedule)
    {
        if ($this->checker) {
            if ($this->checker->check($schedule)) {
                $this->schedules[$schedule->getName()] = $schedule;
            }
        } else {
            $this->schedules[$schedule->getName()] = $schedule;
        }
    }
    /**
     * @inheritDoc
     */
    public function getType()
    {
        return \Builderius\MooMoo\Platform\Bundle\HookBundle\Model\HookInterface::FILTER_TYPE;
    }
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $schedules = \func_get_arg(0);
        foreach ($this->schedules as $name => $schedule) {
            $schedules[$name] = [\Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronSchedule::INTERVAL_FIELD => (int) $schedule->getInterval(), \Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronSchedule::DISPLAY_FIELD => esc_html__($schedule->getDisplay())];
        }
        return $schedules;
    }
}
