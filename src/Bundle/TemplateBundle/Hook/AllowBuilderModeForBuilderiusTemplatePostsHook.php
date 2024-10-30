<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class AllowBuilderModeForBuilderiusTemplatePostsHook extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        global $wp_post_types;
        $wp_post_types[BuilderiusTemplatePostType::POST_TYPE]->publicly_queryable = true;

        $public_query_vars = func_get_arg(0);
        $public_query_vars[] = BuilderiusTemplatePostType::POST_TYPE;

        return $public_query_vars;
    }
}
