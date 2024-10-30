<?php

namespace Builderius\MooMoo\Platform\Bundle\CronBundle;

use Builderius\MooMoo\Platform\Bundle\CronBundle\Registry\CronCommandsRegistryInterface;
use Builderius\MooMoo\Platform\Bundle\CronBundle\Scheduler\CronCommandsSchedulerInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
class CronBundle extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle
{
    /**
     * @inheritDoc
     */
    public function build(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_cron_schedule', 'moomoo_cron.hook.cron_schedules_registration', 'addCronSchedule'));
        $container->addCompilerPass(new \Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass('moomoo_cron_command', 'moomoo_cron.registry.cron_commands', 'addCronCommand'));
    }
    /**
     * @inheritDoc
     */
    public function boot()
    {
        /** @var CronCommandsRegistryInterface $commandsRegistry */
        $commandsRegistry = $this->container->get('moomoo_cron.registry.cron_commands');
        /** @var CronCommandsSchedulerInterface $commandsScheduler */
        $commandsScheduler = $this->container->get('moomoo_cron.scheduler.cron_commands');
        $commandsScheduler->scheduleCommands($commandsRegistry->getCronCommands());
        parent::boot();
    }
}
