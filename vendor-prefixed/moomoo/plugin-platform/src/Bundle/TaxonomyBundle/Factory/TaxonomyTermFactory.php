<?php

namespace Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Factory;

use Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\Term;
class TaxonomyTermFactory
{
    public static function createTerm($name, $taxonomy, $description, $parent, $slug, $aliasOf)
    {
        return new \Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\Term([\Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\Term::NAME_FIELD => $name, \Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\Term::TAXONOMY_FIELD => $taxonomy, \Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\Term::DESCRIPTION_FIELD => $description, \Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\Term::PARENT_FIELD => $parent, \Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\Term::SLUG_FIELD => $slug, \Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\Term::ALIAS_OF_FIELD => $aliasOf]);
    }
}
