<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;

class ForbidAccessToBuilderiusTemplatesPostsHook extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        global $post, $wp_query;

            if ($post && $post->post_type === BuilderiusTemplatePostType::POST_TYPE) {
            $wp_query->set_404();
            status_header( 404 );
            nocache_headers();
        }
    }
}