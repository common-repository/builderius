<?php

namespace Builderius\Bundle\BuilderBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;

class DisableNewVersionUpdateNotificationHook extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $transient = func_get_arg(0);
        $targetPlugin = 'builderius/builderius.php';

        if (isset($transient->response) && is_array($transient->response)) {
            if (isset($transient->response[$targetPlugin])) {
                if (strpos($transient->response[$targetPlugin]->new_version, '1.0') === 0) {
                    unset($transient->response[$targetPlugin]);
                }
            }
        }

        return $transient;
    }
}