<?php

namespace Builderius\MooMoo\Platform\Bundle\WpCliBundle\Registrator;

use Builderius\MooMoo\Platform\Bundle\WpCliBundle\Model\WpCliCommandInterface;
interface WpCliCommandsRegistratorInterface
{
    /**
     * @param WpCliCommandInterface[] $commands
     */
    public function registerCommands(array $commands);
}
