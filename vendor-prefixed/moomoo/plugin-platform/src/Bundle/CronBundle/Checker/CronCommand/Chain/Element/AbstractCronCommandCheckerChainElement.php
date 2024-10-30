<?php

namespace Builderius\MooMoo\Platform\Bundle\CronBundle\Checker\CronCommand\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\CronBundle\Checker\CronCommand\CronCommandCheckerInterface;
use Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface;
abstract class AbstractCronCommandCheckerChainElement implements \Builderius\MooMoo\Platform\Bundle\CronBundle\Checker\CronCommand\CronCommandCheckerInterface
{
    /**
     * @var CronCommandCheckerInterface|null
     */
    protected $successor;
    /**
     * @param CronCommandCheckerInterface $checker
     */
    public function setSuccessor(\Builderius\MooMoo\Platform\Bundle\CronBundle\Checker\CronCommand\CronCommandCheckerInterface $checker)
    {
        $this->successor = $checker;
    }
    /**
     * @return CronCommandCheckerInterface|null
     */
    protected function getSuccessor()
    {
        return $this->successor;
    }
    /**
     * @inheritDoc
     */
    public function check(\Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface $command)
    {
        $result = $this->checkCommand($command);
        if ($this->getSuccessor()) {
            return $this->getSuccessor()->check($command);
        } else {
            return $result;
        }
    }
    /**
     * @param CronCommandInterface $schedule
     * @return bool
     */
    protected abstract function checkCommand(\Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface $schedule);
}
