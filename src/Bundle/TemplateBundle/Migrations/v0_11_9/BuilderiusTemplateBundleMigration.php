<?php

namespace Builderius\Bundle\TemplateBundle\Migrations\v0_11_9;

use Builderius\Bundle\ModuleBundle\Model\BuilderiusSavedCompositeModule;
use Builderius\Bundle\SavedFragmentBundle\Model\BuilderiusSavedFragment;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\Bundle\TemplateBundle\Converter\Version\CompositeBuilderiusTemplateConfigVersionConverter;
use Builderius\Bundle\TemplateBundle\Helper\ContentConfigHelper;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommit;
use Builderius\MooMoo\Platform\Bundle\MigrationBundle\Migration\Migration;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareTrait;

class BuilderiusTemplateBundleMigration implements Migration, ContainerAwareInterface
{
    use ContainerAwareTrait;

    const VERSION = '0.11.9';

    /**
     * @inheritDoc
     */
    public function up(\wpdb $wpdb)
    {
        $posts = $wpdb->get_results(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type IN ('builderius_commit') AND post_content != 'null' AND post_content IS NOT NULL");
        foreach ($posts as $post) {
            $config = ContentConfigHelper::formatConfig(
                json_decode(
                    get_post_meta( $post->ID, BuilderiusCommit::CONTENT_CONFIG_FIELD, true ),
                    true
                )
            );
            if (!isset($config['version']) || !isset($config['version']['builderius']) ||
                    version_compare($config['version']['builderius'], self::VERSION) === -1) {
                foreach ($this->getBuilderiusTemplateConfigVersionConverters() as $versionConverter) {
                    $config = $versionConverter->convert($config);
                }
                if (!isset($config['version'])) {
                    $config['version'] = [];
                }
                $config['version']['builderius'] = self::VERSION;
                update_post_meta(
                    $post->ID,
                    BuilderiusCommit::CONTENT_CONFIG_FIELD,
                    wp_slash(json_encode($config))
                );
            }
        }

        $posts = $wpdb->get_results(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type IN ('builderius_saved_cm')");
        foreach ($posts as $post) {
            $config = ContentConfigHelper::formatConfig(
                json_decode(
                    get_post_meta( $post->ID, BuilderiusSavedCompositeModule::CONFIG_FIELD, true ),
                    true
                )
            );
            if (!isset($config['version']) || !isset($config['version']['builderius']) ||
                version_compare($config['version']['builderius'], self::VERSION) === -1) {
                foreach ($this->getBuilderiusTemplateConfigVersionConverters() as $versionConverter) {
                    $config = $versionConverter->convert($config);
                }
                if (!isset($config['version'])) {
                    $config['version'] = [];
                }
                $config['version']['builderius'] = self::VERSION;
                update_post_meta(
                    $post->ID,
                    BuilderiusSavedCompositeModule::CONFIG_FIELD,
                    wp_slash(json_encode($config))
                );
            }
        }

        $posts = $wpdb->get_results(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type IN ('builderius_saved_fr')");
        foreach ($posts as $post) {
            $config = ContentConfigHelper::formatConfig(
                json_decode(
                    get_post_meta( $post->ID, BuilderiusSavedFragment::STATIC_CONTENT_CONFIG_FIELD, true ),
                    true
                )
            );
            if (is_array($config)) {
                if (!isset($config['version']) || !isset($config['version']['builderius']) ||
                    version_compare($config['version']['builderius'], self::VERSION) === -1) {
                    foreach ($this->getBuilderiusTemplateConfigVersionConverters() as $versionConverter) {
                        $config = $versionConverter->convert($config);
                    }
                    if (!isset($config['version'])) {
                        $config['version'] = [];
                    }
                    $config['version']['builderius'] = self::VERSION;
                    update_post_meta(
                        $post->ID,
                        BuilderiusSavedFragment::STATIC_CONTENT_CONFIG_FIELD,
                        wp_slash(json_encode($config))
                    );
                }
            }

            $config = ContentConfigHelper::formatConfig(
                json_decode(
                    get_post_meta( $post->ID, BuilderiusSavedFragment::DYNAMIC_CONTENT_CONFIG_FIELD, true ),
                    true
                )
            );
            if (is_array($config)) {
                if (!isset($config['version']) || !isset($config['version']['builderius']) ||
                    version_compare($config['version']['builderius'], self::VERSION) === -1) {
                    foreach ($this->getBuilderiusTemplateConfigVersionConverters() as $versionConverter) {
                        $config = $versionConverter->convert($config);
                    }
                    if (!isset($config['version'])) {
                        $config['version'] = [];
                    }
                    $config['version']['builderius'] = self::VERSION;
                    update_post_meta(
                        $post->ID,
                        BuilderiusSavedFragment::DYNAMIC_CONTENT_CONFIG_FIELD,
                        wp_slash(json_encode($config))
                    );
                }
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

        return $compositeConverter->getConverters('builderius', self::VERSION);
    }
}