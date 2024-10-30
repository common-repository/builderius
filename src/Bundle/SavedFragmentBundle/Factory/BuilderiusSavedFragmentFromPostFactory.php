<?php

namespace Builderius\Bundle\SavedFragmentBundle\Factory;

use Builderius\Bundle\TemplateBundle\Helper\ContentConfigHelper;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;
use Builderius\Bundle\SavedFragmentBundle\Model\BuilderiusSavedFragment;
use Builderius\Bundle\SavedFragmentBundle\Model\BuilderiusSavedFragmentInterface;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentCategoryTaxonomy;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentTagTaxonomy;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentTypeTaxonomy;

class BuilderiusSavedFragmentFromPostFactory
{
    /**
     * @param \WP_Post $post
     * @return BuilderiusSavedFragmentInterface
     */
    public static function createSavedFragment(\WP_Post $post)
    {
        $image = get_post_meta(
            $post->ID,
            BuilderiusSavedFragment::IMAGE_FIELD,
            true
        );
        $savedFragment = new BuilderiusSavedFragment([
            BuilderiusSavedFragment::ID_FIELD => $post->ID,
            BuilderiusSavedFragment::NAME_FIELD => $post->post_name,
            BuilderiusSavedFragment::TITLE_FIELD => $post->post_title,
            BuilderiusSavedFragment::DESCRIPTION_FIELD => $post->post_content,
            BuilderiusSavedFragment::AUTHOR_FIELD => get_user_by('ID', $post->post_author),
            BuilderiusSavedFragment::CREATED_AT_FIELD => $post->post_date,
            BuilderiusSavedFragment::UPDATED_AT_FIELD => $post->post_modified,
            BuilderiusSavedFragment::IMAGE_FIELD => !empty($image) ? $image : null,
            BuilderiusSavedFragment::DYNAMIC_CONTENT_CONFIG_FIELD =>
                ContentConfigHelper::formatConfig(
                    json_decode(
                        get_post_meta(
                            $post->ID,
                            BuilderiusSavedFragment::DYNAMIC_CONTENT_CONFIG_FIELD,
                            true
                        ),
                        true
                    )
                ),
            BuilderiusSavedFragment::STATIC_CONTENT_CONFIG_FIELD =>
                ContentConfigHelper::formatConfig(
                    json_decode(
                        get_post_meta(
                            $post->ID,
                            BuilderiusSavedFragment::STATIC_CONTENT_CONFIG_FIELD,
                            true
                        ),
                        true
                    )
                ),
        ]);
        $types = wp_get_post_terms($post->ID, BuilderiusSavedFragmentTypeTaxonomy::NAME);
        if (!empty($types)) {
            /** @var \WP_Term $type */
            $type = reset($types);
            $savedFragment->setType($type->name);
        }
        $technologies = wp_get_post_terms($post->ID, BuilderiusTemplateTechnologyTaxonomy::NAME);
        if (!empty($technologies)) {
            /** @var \WP_Term $technology */
            foreach ($technologies as $technology) {
                $savedFragment->addTechnology($technology->name);
            }
        }
        $categories = wp_get_post_terms($post->ID, BuilderiusSavedFragmentCategoryTaxonomy::NAME);
        if (!empty($categories)) {
            /** @var \WP_Term $category */
            $category = reset($categories);
            $savedFragment->setCategory($category->name);
        }
        /** @var \WP_Term[] $tags */
        $tags = wp_get_post_terms($post->ID, BuilderiusSavedFragmentTagTaxonomy::NAME);
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $savedFragment->addTag($tag->name);
            }
        }

        return $savedFragment;
    }
}