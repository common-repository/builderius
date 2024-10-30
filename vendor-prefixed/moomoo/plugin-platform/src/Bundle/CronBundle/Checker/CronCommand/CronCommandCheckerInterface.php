<?php

namespace Builderius\MooMoo\Platform\Bundle\CronBundle\Checker\CronCommand;

use Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface;
interface CronCommandCheckerInterface
{
    /**
     * @param CronCommandInterface $command
     * @return boolean
     */
    public function check(\Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface $command);
}
