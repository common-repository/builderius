<?php

namespace Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareTrait;
abstract class AbstractTaxonomy implements \Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\TaxonomyInterface, \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface
{
    use ConditionAwareTrait;
    /**
     * @var TermInterface[]
     */
    protected $terms = [];
    /**
     * @inheritDoc
     */
    public function addTerm(\Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\TermInterface $term)
    {
        $term->setTaxonomy($this->getName());
        $this->terms[$term->getName()] = $term;
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function getTerms()
    {
        return $this->terms;
    }
}
