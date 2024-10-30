<?php

namespace Builderius\MooMoo\Platform\Bundle\CronBundle\Scheduler;

use Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface;
interface CronCommandsSchedulerInterface
{
    /**
     * @param CronCommandInterface[] $commands
     * @return void
     */
    public function scheduleCommands(array $commands);
}
