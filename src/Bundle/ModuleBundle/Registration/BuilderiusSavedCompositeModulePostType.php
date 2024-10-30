<?php

namespace Builderius\Bundle\ModuleBundle\Registration;

use Builderius\MooMoo\Platform\Bundle\PostBundle\PostType\PostTypeInterface;

class BuilderiusSavedCompositeModulePostType implements PostTypeInterface
{
    const POST_TYPE = 'builderius_saved_cm';

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
            'rewrite'            => ['slug' => 'builderius-saved-composite-module', 'with_front' => false],
            'supports'           => ['title', 'editor', 'author', 'custom-fields'],
            'taxonomies'         => [],
        ];
    }
}
