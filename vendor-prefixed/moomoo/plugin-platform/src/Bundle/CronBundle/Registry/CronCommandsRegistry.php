<?php

namespace Builderius\MooMoo\Platform\Bundle\CronBundle\Registry;

use Exception;
use Builderius\MooMoo\Platform\Bundle\CronBundle\Checker\CronCommand\CronCommandCheckerInterface;
use Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface;
class CronCommandsRegistry implements \Builderius\MooMoo\Platform\Bundle\CronBundle\Registry\CronCommandsRegistryInterface
{
    /**
     * @var CronCommandInterface[]
     */
    protected $commands = [];
    /**
     * @var CronCommandCheckerInterface
     */
    private $checker;
    /**
     * @param CronCommandCheckerInterface $checker
     */
    public function __construct(\Builderius\MooMoo\Platform\Bundle\CronBundle\Checker\CronCommand\CronCommandCheckerInterface $checker)
    {
        $this->checker = $checker;
    }
    /**
     * @param CronCommandInterface $command
     * @throws Exception
     */
    public function addCronCommand(\Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface $command)
    {
        if ($this->checker->check($command)) {
            if (isset($this->commands[$command->getName()])) {
                throw new \Exception(\sprintf('CronCommand with name "%s" already exists', $command->getName()));
            }
            $this->commands[$command->getName()] = $command;
        }
    }
    /**
     * @inheritDoc
     */
    public function getCronCommands()
    {
        return $this->commands;
    }
    /**
     * @inheritDoc
     */
    public function getCronCommand($name)
    {
        if ($this->hasCronCommand($name)) {
            return $this->commands[$name];
        }
        return null;
    }
    /**
     * @inheritDoc
     */
    public function hasCronCommand($name)
    {
        if (isset($this->commands[$name])) {
            return \true;
        }
        return \false;
    }
}
