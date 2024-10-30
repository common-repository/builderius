<?php

namespace Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Registrator;

use Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\TaxonomyInterface;
interface TaxonomiesRegistratorInterface
{
    /**
     * @param TaxonomyInterface[] $taxonomies
     */
    public function registerTaxonomies(array $taxonomies);
}
