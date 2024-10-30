<?php

namespace Builderius\Bundle\SavedFragmentBundle\Hook;

use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTypeTaxonomy;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentPostType;

class TemplateTaxonomiesAssigningHook extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        register_taxonomy_for_object_type(
            BuilderiusTemplateTechnologyTaxonomy::NAME,
            BuilderiusSavedFragmentPostType::POST_TYPE
        );
    }
}