<?php

namespace Builderius\Bundle\ModuleBundle\Factory;

use Builderius\Bundle\ModuleBundle\Model\BuilderiusSavedCompositeModule;
use Builderius\Bundle\TemplateBundle\Helper\ContentConfigHelper;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;

class BuilderiusSavedCompositeModuleFromPostFactory
{
    /**
     * @param \WP_Post $post
     * @return BuilderiusSavedCompositeModule
     */
    public static function createSavedCompositeModule(\WP_Post $post)
    {
        $icon = get_post_meta(
            $post->ID,
            BuilderiusSavedCompositeModule::ICON_FIELD,
            true
        );
        $category = get_post_meta(
            $post->ID,
            BuilderiusSavedCompositeModule::CATEGORY_FIELD,
            true
        );
        $tags = json_decode(
            get_post_meta(
                $post->ID,
                BuilderiusSavedCompositeModule::TAGS_FIELD,
                true
            ),
            true
        );
        $savedCompositeModule = new BuilderiusSavedCompositeModule([
            BuilderiusSavedCompositeModule::ID_FIELD => $post->ID,
            BuilderiusSavedCompositeModule::NAME_FIELD => str_replace(' ', '', $post->post_title) . 'SavedComposite',
            BuilderiusSavedCompositeModule::LABEL_FIELD => $post->post_title,
            BuilderiusSavedCompositeModule::CATEGORY_FIELD => !empty($category)? $category : 'custom',
            BuilderiusSavedCompositeModule::ICON_FIELD => !empty($icon) ? $icon : null,
            BuilderiusSavedCompositeModule::CONFIG_FIELD =>
                ContentConfigHelper::formatConfig(
                    json_decode(
                        get_post_meta(
                            $post->ID,
                            BuilderiusSavedCompositeModule::CONFIG_FIELD,
                            true
                        ),
                        true
                    )
                ),
            BuilderiusSavedCompositeModule::TAGS_FIELD => is_array($tags) ? $tags : []

        ]);
        $technologies = wp_get_post_terms($post->ID, BuilderiusTemplateTechnologyTaxonomy::NAME);
        if (!empty($technologies)) {
            /** @var \WP_Term $technology */
            foreach ($technologies as $technology) {
                $savedCompositeModule->addTechnology($technology->name);
            }
        }

        return $savedCompositeModule;
    }
}