<?php

namespace Builderius\MooMoo\Platform\Bundle\CronBundle\Checker\CronSchedule;

use Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronScheduleInterface;
interface CronScheduleCheckerInterface
{
    /**
     * @param CronScheduleInterface $schedule
     * @return boolean
     */
    public function check(\Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronScheduleInterface $schedule);
}
