<?php

namespace Builderius\Bundle\ReleaseBundle\Registration;

use Builderius\MooMoo\Platform\Bundle\PostBundle\PostType\PostTypeInterface;

class BulderiusReleasePostType implements PostTypeInterface
{
    const POST_TYPE = 'builderius_release';

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return self::POST_TYPE;
    }

    /**
     * @inheritDoc
     */
    public function getArguments()
    {
        return [
            'public' => false,
            'rewrite' => false,
            'show_ui' => false,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'exclude_from_search' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'show_in_rest' => false,
            'supports' => ['title', 'thumbnail', 'author']
        ];
    }
}