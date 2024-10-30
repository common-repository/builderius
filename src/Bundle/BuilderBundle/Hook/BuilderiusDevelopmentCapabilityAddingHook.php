<?php

namespace Builderius\Bundle\BuilderBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class BuilderiusDevelopmentCapabilityAddingHook extends AbstractAction
{
    const BUILDERIUS_DEVELOPMENT_CAPABILITY = 'builderius-development';

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $role = get_role('administrator');
        $role->add_cap(self::BUILDERIUS_DEVELOPMENT_CAPABILITY);
    }
}