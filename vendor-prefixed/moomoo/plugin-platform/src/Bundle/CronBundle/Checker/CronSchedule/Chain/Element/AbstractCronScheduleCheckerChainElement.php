<?php

namespace Builderius\MooMoo\Platform\Bundle\CronBundle\Checker\CronSchedule\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\CronBundle\Checker\CronSchedule\CronScheduleCheckerInterface;
use Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronScheduleInterface;
abstract class AbstractCronScheduleCheckerChainElement implements \Builderius\MooMoo\Platform\Bundle\CronBundle\Checker\CronSchedule\CronScheduleCheckerInterface
{
    /**
     * @var CronScheduleCheckerInterface|null
     */
    protected $successor;
    /**
     * @param CronScheduleCheckerInterface $checker
     */
    public function setSuccessor(\Builderius\MooMoo\Platform\Bundle\CronBundle\Checker\CronSchedule\CronScheduleCheckerInterface $checker)
    {
        $this->successor = $checker;
    }
    /**
     * @return CronScheduleCheckerInterface|null
     */
    protected function getSuccessor()
    {
        return $this->successor;
    }
    /**
     * @inheritDoc
     */
    public function check(\Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronScheduleInterface $schedule)
    {
        $result = $this->checkSchedule($schedule);
        if ($this->getSuccessor()) {
            return $this->getSuccessor()->check($schedule);
        } else {
            return $result;
        }
    }
    /**
     * @param CronScheduleInterface $schedule
     * @return bool
     */
    protected abstract function checkSchedule(\Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronScheduleInterface $schedule);
}
