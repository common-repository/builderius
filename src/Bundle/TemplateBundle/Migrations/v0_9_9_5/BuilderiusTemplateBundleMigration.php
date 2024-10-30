<?php

namespace Builderius\Bundle\TemplateBundle\Migrations\v0_9_9_5;

use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\Bundle\TemplateBundle\Converter\Version\CompositeBuilderiusTemplateConfigVersionConverter;
use Builderius\Bundle\TemplateBundle\Helper\ContentConfigHelper;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateContentProviderInterface;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateSubTypeTaxonomy;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTypeTaxonomy;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommit;
use Builderius\MooMoo\Platform\Bundle\MigrationBundle\Migration\Migration;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareTrait;

class BuilderiusTemplateBundleMigration implements Migration, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var BuilderiusTemplateContentProviderInterface
     */
    private $contentProvider;

    /**
     * @inheritDoc
     */
    public function up(\wpdb $wpdb)
    {
        $posts = $wpdb->get_results(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type IN ('builderius_template')");

        foreach ($posts as $post) {
            $existingTypes = wp_get_object_terms($post->ID, BuilderiusTemplateTypeTaxonomy::NAME, ['fields' => 'slugs']);
            if (!in_array('template_part', $existingTypes)) {
                wp_set_object_terms($post->ID, 'template', BuilderiusTemplateTypeTaxonomy::NAME);
                $existingSubTypes = wp_get_object_terms($post->ID, BuilderiusTemplateSubTypeTaxonomy::NAME, ['fields' => 'slugs']);
                if (empty($existingSubTypes) || !in_array('hook', $existingSubTypes)) {
                    wp_set_object_terms($post->ID, 'regular', BuilderiusTemplateSubTypeTaxonomy::NAME);
                }
            }
        }

        $posts = $wpdb->get_results(
            "SELECT ID, post_type, post_content FROM {$wpdb->posts} WHERE post_type IN ('builderius_commit')");
        foreach ($posts as $post) {
            $config = ContentConfigHelper::formatConfig(
                json_decode(
                    get_post_meta( $post->ID, BuilderiusCommit::CONTENT_CONFIG_FIELD, true ),
                    true
                )
            );
            if (!isset($config['version']) || !isset($config['version']['builderius']) ||
                version_compare($config['version']['builderius'], '0.9.9.5') === -1) {
                foreach ($this->getBuilderiusTemplateConfigVersionConverters() as $versionConverter) {
                    $config = $versionConverter->convert($config);
                }
                if (!isset($config['version'])) {
                    $config['version'] = [];
                }
                $config['version']['builderius'] = '0.9.9.5';
                update_post_meta(
                    $post->ID,
                    BuilderiusCommit::CONTENT_CONFIG_FIELD,
                    wp_slash(json_encode($config))
                );
            }
            if (isset($config['modules'])) {
                $content = $this->getContentProvider()->getContent('html', $config);
                remove_all_filters('content_save_pre');
                $wpdb->update(
                    $wpdb->posts,
                    ['post_content' => json_encode($content)],
                    ['ID' => $post->ID]
                );
            }
        }
    }

    /**
     * @return BuilderiusTemplateConfigVersionConverterInterface[]
     */
    private function getBuilderiusTemplateConfigVersionConverters()
    {
        /** @var CompositeBuilderiusTemplateConfigVersionConverter $compositeConverter */
        $compositeConverter = $this->container->get('builderius_template.version_converter.composite');

        return $compositeConverter->getConverters('builderius', '0.9.9.5');
    }

    /**
     * @return BuilderiusTemplateContentProviderInterface|null
     */
    private function getContentProvider()
    {
        if (null === $this->contentProvider) {
            $this->contentProvider = $this->container->get('builderius_template.provider.template_content.composite');
        }

        return $this->contentProvider;
    }
}