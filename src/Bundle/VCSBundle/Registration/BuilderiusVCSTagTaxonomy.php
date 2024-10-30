<?php

namespace Builderius\Bundle\VCSBundle\Registration;

use Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\AbstractTaxonomy;

class BuilderiusVCSTagTaxonomy extends AbstractTaxonomy
{
    const NAME = 'builderius_vcs_tag';

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
        return BuilderiusCommitPostType::POST_TYPE;
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
            'query_var' => is_admin(),
            'rewrite' => false,
            'public' => false,
        ];
    }
}
