<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\AbstractTaxonomy;

class BuilderiusTemplateSubTypeTaxonomy extends AbstractTaxonomy
{
    const NAME = 'builderius_template_subtype';

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
        return BuilderiusTemplatePostType::POST_TYPE;
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
