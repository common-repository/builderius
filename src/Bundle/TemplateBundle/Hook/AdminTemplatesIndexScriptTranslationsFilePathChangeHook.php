<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;

class AdminTemplatesIndexScriptTranslationsFilePathChangeHook extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $file = func_get_arg(0);
        $handle = func_get_arg(1);
        $domain = func_get_arg(2);
        if ($domain === 'builderius' && $handle === 'builderius-admin-templates-index') {
            $file = str_replace('-' . $handle, '', $file);
        }

        return $file;
    }
}