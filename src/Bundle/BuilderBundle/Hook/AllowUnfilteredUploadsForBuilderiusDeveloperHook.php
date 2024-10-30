<?php

namespace Builderius\Bundle\BuilderBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;

class AllowUnfilteredUploadsForBuilderiusDeveloperHook extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $caps = func_get_arg(0);
        $cap = func_get_arg(1);
        $userId = func_get_arg(2);
        $user = get_user_by('ID', $userId);
        if ($cap == 'unfiltered_upload' && $user->has_cap(BuilderiusDevelopmentCapabilityAddingHook::BUILDERIUS_DEVELOPMENT_CAPABILITY)) {
            $caps = [];
            $caps[] = $cap;
        }

        return $caps;
    }
}