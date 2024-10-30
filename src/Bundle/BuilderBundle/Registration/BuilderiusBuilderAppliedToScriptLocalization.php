<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

class BuilderiusBuilderAppliedToScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'appliedTo';

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        /** @var \WP_Post $post */
        $post = get_post();
        if ($post) {
            return [
                'id' => $post->ID,
                'type' => $post->post_type,
                'guid' => str_replace('#038;', '&', $post->guid)
            ];
        }

        return [];
    }
}
