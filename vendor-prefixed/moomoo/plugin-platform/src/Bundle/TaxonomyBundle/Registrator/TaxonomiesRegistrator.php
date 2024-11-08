<?php

namespace Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Registrator;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\TaxonomyInterface;
use Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\Term;
class TaxonomiesRegistrator implements \Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Registrator\TaxonomiesRegistratorInterface
{
    /**
     * @inheritDoc
     */
    public function registerTaxonomies(array $taxonomies)
    {
        add_action('init', function () use($taxonomies) {
            /** @var TaxonomyInterface $taxonomy */
            foreach ($taxonomies as $taxonomy) {
                if ($taxonomy instanceof \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface && $taxonomy->hasConditions()) {
                    $evaluated = \true;
                    foreach ($taxonomy->getConditions() as $condition) {
                        if ($condition->evaluate() === \false) {
                            $evaluated = \false;
                            break;
                        }
                    }
                    if (!$evaluated) {
                        continue;
                    }
                    $this->registerTaxonomy($taxonomy);
                } else {
                    $this->registerTaxonomy($taxonomy);
                }
            }
        });
        add_action('admin_init', function () use($taxonomies) {
            /** @var TaxonomyInterface $taxonomy */
            foreach ($taxonomies as $taxonomy) {
                if ($taxonomy instanceof \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface && $taxonomy->hasConditions()) {
                    $evaluated = \true;
                    foreach ($taxonomy->getConditions() as $condition) {
                        if ($condition->evaluate() === \false) {
                            $evaluated = \false;
                            break;
                        }
                    }
                    if (!$evaluated) {
                        continue;
                    }
                    $this->registerTaxonomy($taxonomy, \true);
                } else {
                    $this->registerTaxonomy($taxonomy, \true);
                }
            }
        });
    }
    /**
     * @param TaxonomyInterface $taxonomy
     * @param bool $registerTerms
     */
    private function registerTaxonomy(\Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\TaxonomyInterface $taxonomy, $registerTerms = \false)
    {
        if (!taxonomy_exists($taxonomy->getName())) {
            register_taxonomy($taxonomy->getName(), $taxonomy->getObjectType(), $taxonomy->getArguments());
        }
        if ($registerTerms && !empty($taxonomy->getTerms())) {
            foreach ($taxonomy->getTerms() as $term) {
                $termChecked = wp_cache_get(\sprintf('%s_%s_%s_term_existence_cached', $term->getName(), $term->getTaxonomy(), $term->getParent()));
                $termExists = wp_cache_get(\sprintf('%s_%s_%s_term_exists', $term->getName(), $term->getTaxonomy(), $term->getParent()));
                if (\false === $termChecked) {
                    $termExists = term_exists($term->getName(), $term->getTaxonomy(), $term->getParent());
                    wp_cache_set(\sprintf('%s_%s_%s_term_exists', $term->getName(), $term->getTaxonomy(), $term->getParent()), $termExists);
                    wp_cache_set(\sprintf('%s_%s_%s_term_existence_cached', $term->getName(), $term->getTaxonomy(), $term->getParent()), \true);
                }
                if (!$termExists) {
                    $termResult = wp_insert_term($term->getName(), $term->getTaxonomy(), [\Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\Term::ALIAS_OF_FIELD => $term->getAliasOf(), \Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\Term::DESCRIPTION_FIELD => $term->getDescription(), \Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\Term::PARENT_FIELD => $term->getParent(), \Builderius\MooMoo\Platform\Bundle\TaxonomyBundle\Model\Term::SLUG_FIELD => $term->getSlug()]);
                    if (!$termResult instanceof \WP_Error && isset($termResult['term_id'])) {
                        $termResult = $termResult['term_id'];
                    }
                } else {
                    $termResult = get_term_by('name', $term->getName(), $term->getTaxonomy());
                    $termResult = $termResult->term_id;
                }
                if (!empty($term->getTermMeta())) {
                    foreach ($term->getTermMeta() as $termMeta) {
                        if (!metadata_exists('term', $termResult, $termMeta->getKey())) {
                            add_term_meta($termResult, $termMeta->getKey(), $termMeta->getValue(), $termMeta->isUnique());
                        }
                    }
                }
            }
        }
    }
}
