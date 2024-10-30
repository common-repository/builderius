<?php

namespace Builderius\MooMoo\Platform\Bundle\CronBundle\Scheduler\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface;
interface CronCommandsSchedulerChainElementInterface
{
    /**
     * @param CronCommandInterface $command
     * @return bool
     */
    public function isApplicable(\Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface $command);
    /**
     * @param CronCommandInterface $command
     * @param string $pluginBaseName
     */
    public function schedule(\Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface $command, $pluginBaseName);
}
