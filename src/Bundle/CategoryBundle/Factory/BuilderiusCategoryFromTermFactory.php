<?php

namespace Builderius\Bundle\CategoryBundle\Factory;

use Builderius\Bundle\CategoryBundle\Model\BuilderiusCategory;
use Builderius\Bundle\CategoryBundle\Model\BuilderiusCategoryInterface;

class BuilderiusCategoryFromTermFactory
{
    /**
     * @param \WP_Term $term
     * @return BuilderiusCategoryInterface
     */
    public static function createCategory(\WP_Term $term)
    {
        return new BuilderiusCategory(
            [
                BuilderiusCategory::ID_FIELD => $term->term_id,
                BuilderiusCategory::NAME_FIELD => $term->name,
                BuilderiusCategory::LABEL_FIELD => get_term_meta($term->term_id, BuilderiusCategory::LABEL_FIELD, true),
                BuilderiusCategory::SORT_ORDER_FIELD => (int)get_term_meta($term->term_id, BuilderiusCategory::SORT_ORDER_FIELD, true),
                BuilderiusCategory::DEFAULT_FIELD => (bool)get_term_meta($term->term_id, BuilderiusCategory::DEFAULT_FIELD, true),
                BuilderiusCategory::EDITABLE_FIELD => true
            ]
        );
    }
}