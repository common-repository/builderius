<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class AllowAllPostStatusesInPreviewModeHook extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        /** @var \WP_Query $q */
        $q = func_get_arg(0);
        if ($q->is_main_query() && $q->is_singular()) {
            $q->set('post_status', get_post_stati());
        }
    }
}
