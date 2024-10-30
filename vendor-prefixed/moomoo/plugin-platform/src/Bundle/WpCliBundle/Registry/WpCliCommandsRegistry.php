<?php

namespace Builderius\MooMoo\Platform\Bundle\WpCliBundle\Registry;

use Builderius\MooMoo\Platform\Bundle\WpCliBundle\Model\WpCliCommandInterface;
class WpCliCommandsRegistry implements \Builderius\MooMoo\Platform\Bundle\WpCliBundle\Registry\WpCliCommandsRegistryInterface
{
    /**
     * @var WpCliCommandInterface[]
     */
    private $commands = [];
    /**
     * @param WpCliCommandInterface $command
     */
    public function addCommand(\Builderius\MooMoo\Platform\Bundle\WpCliBundle\Model\WpCliCommandInterface $command)
    {
        $this->commands[$command->getName()] = $command;
    }
    /**
     * @inheritDoc
     */
    public function getCommands()
    {
        return $this->commands;
    }
    /**
     * @inheritDoc
     */
    public function getCommand($name)
    {
        if ($this->hasCommand($name)) {
            return $this->commands[$name];
        }
        return null;
    }
    /**
     * @inheritDoc
     */
    public function hasCommand($name)
    {
        return isset($this->commands[$name]);
    }
}
