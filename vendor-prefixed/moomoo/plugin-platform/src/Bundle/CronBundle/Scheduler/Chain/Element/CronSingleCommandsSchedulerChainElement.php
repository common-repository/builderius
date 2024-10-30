<?php

namespace Builderius\MooMoo\Platform\Bundle\CronBundle\Scheduler\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface;
use Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronSingleCommandInterface;
class CronSingleCommandsSchedulerChainElement extends \Builderius\MooMoo\Platform\Bundle\CronBundle\Scheduler\Chain\Element\AbstractCronCommandsSchedulerChainElement
{
    /**
     * @inheritDoc
     */
    public function isApplicable(\Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface $command)
    {
        if ($command instanceof \Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronSingleCommandInterface) {
            return \true;
        }
        return \false;
    }
    /**
     * @inheritDoc
     */
    public function schedule(\Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface $command, $pluginBaseName)
    {
        add_action($command->getName(), [$command, 'execute']);
        add_action('init', function () use($command) {
            if (!wp_next_scheduled($command->getName())) {
                wp_schedule_single_event($command->getTimestamp(), $command->getName(), $command->getArguments());
            }
        });
    }
}
