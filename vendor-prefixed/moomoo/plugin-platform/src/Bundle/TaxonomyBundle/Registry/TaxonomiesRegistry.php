<?php

namespace Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Registry;

use Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\TaxonomyInterface;
class TaxonomiesRegistry implements \Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Registry\TaxonomiesRegistryInterface
{
    /**
     * @var TaxonomyInterface[]
     */
    private $taxonomies = [];
    /**
     * @param TaxonomyInterface $taxonomy
     */
    public function addTaxonomy(\Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\TaxonomyInterface $taxonomy)
    {
        $this->taxonomies[$taxonomy->getName()] = $taxonomy;
    }
    /**
     * @inheritDoc
     */
    public function getTaxonomies()
    {
        return $this->taxonomies;
    }
    /**
     * @inheritDoc
     */
    public function getTaxonomy($name)
    {
        if ($this->hasTaxonomy($name)) {
            return $this->taxonomies[$name];
        }
        return null;
    }
    /**
     * @inheritDoc
     */
    public function hasTaxonomy($name)
    {
        return isset($this->taxonomies[$name]);
    }
}
