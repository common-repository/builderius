<?php

namespace Builderius\Bundle\BuilderBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;

class AddAllowedMimeTypesForBuilderiusDeveloperHook extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $mimes = func_get_arg(0);
        $user = wp_get_current_user();
        if ($user->has_cap(BuilderiusDevelopmentCapabilityAddingHook::BUILDERIUS_DEVELOPMENT_CAPABILITY)) {
            $mimes['json'] = 'application/json';
            $mimes['svg'] = 'image/svg+xml';
            $mimes['webp'] = 'image/webp';
            $mimes['otf'] = 'font/otf';
            $mimes['ttf'] =	'font/ttf';
            $mimes['woff'] = 'font/woff';
            $mimes['woff2'] = 'font/woff2';
            $mimes['eot'] = 'application/vnd.ms-fontobject';
        }

        return $mimes;
    }
}