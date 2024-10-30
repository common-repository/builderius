<?php

namespace Builderius\Bundle\SavedFragmentBundle\Registration;

use Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\AbstractTaxonomy;

class BuilderiusSavedFragmentTagTaxonomy extends AbstractTaxonomy
{
    const NAME = 'builderius_saved_fr_tag';

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @inheritDoc
     */
    public function getObjectType()
    {
        return BuilderiusSavedFragmentPostType::POST_TYPE;
    }

    /**
     * @inheritDoc
     */
    public function getArguments()
    {
        return [
            'hierarchical' => false,
            'show_ui' => false,
            'show_in_nav_menus' => false,
            'show_admin_column' => false,
            'show_in_rest' => false,
            'public' => false,
        ];
    }
}
