<?php

namespace Builderius\MooMoo\Platform\Bundle\CronBundle\Scheduler\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface;
use Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronRecurrentCommandInterface;
class CronRecurrentCommandsSchedulerChainElement extends \Builderius\MooMoo\Platform\Bundle\CronBundle\Scheduler\Chain\Element\AbstractCronCommandsSchedulerChainElement
{
    /**
     * @inheritDoc
     */
    public function isApplicable(\Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface $command)
    {
        if ($command instanceof \Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronRecurrentCommandInterface) {
            return \true;
        }
        return \false;
    }
    /**
     * @param CronCommandInterface|CronRecurrentCommandInterface $command
     * @param string $pluginBaseName
     */
    public function schedule(\Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface $command, $pluginBaseName)
    {
        add_action($command->getName(), [$command, 'execute']);
        add_action('init', function () use($command) {
            if (!wp_next_scheduled($command->getName())) {
                wp_schedule_event($command->getTimestamp(), $command->getRecurrence(), $command->getName(), $command->getArguments());
            }
        });
        add_action('deactivate_' . $pluginBaseName, function () use($command) {
            wp_clear_scheduled_hook($command->getName());
        });
    }
}
