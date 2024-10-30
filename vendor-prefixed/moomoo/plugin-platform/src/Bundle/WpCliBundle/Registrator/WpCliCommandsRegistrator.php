<?php

namespace Builderius\MooMoo\Platform\Bundle\WpCliBundle\Registrator;

use Builderius\MooMoo\Platform\Bundle\WpCliBundle\Model\WpCliCommandInterface;
class WpCliCommandsRegistrator implements \Builderius\MooMoo\Platform\Bundle\WpCliBundle\Registrator\WpCliCommandsRegistratorInterface
{
    /**
     * @inheritDoc
     */
    public function registerCommands(array $commands)
    {
        add_action('cli_init', function () use($commands) {
            foreach ($commands as $command) {
                if ($command instanceof \Builderius\MooMoo\Platform\Bundle\WpCliBundle\Model\WpCliCommandInterface) {
                    \WP_CLI::add_command($command->getName(), [$command, 'execute'], $command->getAdditionalRegistrationParameters());
                }
            }
        });
    }
}
