<?php

namespace Builderius\Bundle\SavedFragmentBundle\Registration;

use Builderius\MooMoo\Platform\Bundle\PostBundle\PostType\PostTypeInterface;

class BuilderiusSavedFragmentPostType implements PostTypeInterface
{
    const POST_TYPE = 'builderius_saved_fr';

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
        return  [
            'public'             => false,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'capability_type'    => 'page',
            'hierarchical'       => true,
            'has_archive'        => true,
            'rewrite'            => ['slug' => 'builderius-saved-fragment', 'with_front' => false],
            'supports'           => ['title', 'editor', 'author', 'custom-fields'],
            'taxonomies'         => [],
        ];
    }
}
