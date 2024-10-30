<?php

namespace Builderius\Bundle\TemplateBundle\Migrations\v0_9_4_1;

use Builderius\Bundle\SettingBundle\Converter\Facade\ToArray\BuilderiusSettingFacadeToArrayConverter;
use Builderius\Bundle\SettingBundle\Converter\Setting\ToArray\BuilderiusSettingToArrayConverterInterface;
use Builderius\Bundle\SettingBundle\Event\SettingContainingEvent;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Registry\BuilderiusSettingsRegistryInterface;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\Bundle\TemplateBundle\Converter\Version\CompositeBuilderiusTemplateConfigVersionConverter;
use Builderius\Bundle\TemplateBundle\Converter\Version\v0_9_4_1\BuilderiusTemplateConfigMediaQueriesConverter;
use Builderius\Bundle\TemplateBundle\Helper\ContentConfigHelper;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranch;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommit;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\MooMoo\Platform\Bundle\MigrationBundle\Migration\Migration;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderiusTemplateBundleMigration implements Migration, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var array|null
     */
    private $globalSettingsWithDefaultValues;

    /**
     * @inheritDoc
     */
    public function up(\wpdb $wpdb)
    {
        $this->updateGlobalSettings();
        $this->updateTemplateConfig($wpdb);
    }
    private function updateTemplateConfig(\wpdb $wpdb)
    {
        $posts = $wpdb->get_results(
            "SELECT ID, post_type, post_content FROM {$wpdb->posts} WHERE post_type IN ('builderius_branch', 'builderius_commit') AND post_content != 'null' AND post_content IS NOT NULL");
        foreach ($posts as $post) {
            if ($post->post_type === BuilderiusBranchPostType::POST_TYPE) {
                $config = ContentConfigHelper::formatConfig(
                    json_decode(
                        get_post_meta( $post->ID, BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD, true ),
                        true
                    )
                );
            } else {
                $config = ContentConfigHelper::formatConfig(
                    json_decode(
                        get_post_meta( $post->ID, BuilderiusCommit::CONTENT_CONFIG_FIELD, true ),
                        true
                    )
                );
            }
            if (isset($config['modules']) && (!isset($config['version']) || !isset($config['version']['builderius']) ||
                    version_compare($config['version']['builderius'], '0.9.4.1') === -1)) {
                foreach ($this->getBuilderiusTemplateConfigVersionConverters() as $versionConverter) {
                    $config = $versionConverter->convert($config);
                }
                if (!isset($config['version'])) {
                    $config['version'] = [];
                }
                $config['version']['builderius'] = '0.9.4.1';
                if ($post->post_type === BuilderiusBranchPostType::POST_TYPE) {
                    update_post_meta(
                        $post->ID,
                        BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD,
                        wp_slash(json_encode($config))
                    );
                } else {
                    update_post_meta(
                        $post->ID,
                        BuilderiusCommit::CONTENT_CONFIG_FIELD,
                        wp_slash(json_encode($config))
                    );
                }
            }
        }
    }

    private function updateGlobalSettings()
    {
        $settings = get_option('builderius_global_settings');
        $filledGlobalSettings = [];
        $respModesNames = array_keys(BuilderiusTemplateConfigMediaQueriesConverter::RESP_MODES_TO_MEDIA_QUERIES);
        if ($settings) {
            $settings = json_decode($settings, true);
            if (is_array($settings)) {
                if (!isset($settings['version']) || !isset($settings['version']['builderius']) ||
                    version_compare($settings['version']['builderius'], '0.9.4.1') === -1) {
                    foreach ($settings as $technology => $technologySettings) {
                        if ($technology === 'version') {
                            continue;
                        }
                        foreach ($technologySettings['settings'] as $idx => $technologySetting) {
                            $filledGlobalSettings[] = $technologySetting['name'];
                            $globalSetting = $this->getSettingsRegistry()->getSetting(
                                'singular',
                                'html',
                                $technologySetting['name']
                            );
                            if ($globalSetting && $globalSetting instanceof BuilderiusSettingCssAwareInterface) {
                                foreach ($technologySetting['value'] as $type => $respValues) {
                                    foreach ($respValues as $respModeName => $value) {
                                        if (in_array($respModeName, $respModesNames)) {
                                            $settings[$technology]['settings'][$idx]['value'][$type][BuilderiusTemplateConfigMediaQueriesConverter::RESP_MODES_TO_MEDIA_QUERIES[$respModeName]] = $value;
                                            unset($settings[$technology]['settings'][$idx]['value'][$type][$respModeName]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $settings = [];
        }
        $globalSettingsWithDefaultValues = $this->getGlobalSettingsWithDefaultValuesList('singular', 'html');
        if (!empty($globalSettingsWithDefaultValues)) {
            foreach (array_keys($globalSettingsWithDefaultValues) as $settName) {
                if (!in_array($settName, $filledGlobalSettings)) {
                    $settings['html']['settings'][] = [
                        'name' => $settName,
                        'value' => [
                            'all' => $globalSettingsWithDefaultValues[$settName],
                            'singular' => $globalSettingsWithDefaultValues[$settName]
                        ]
                    ];
                }
            }
        }
        $settings['version']['builderius'] = '0.9.4.1';
        update_option('builderius_global_settings', json_encode($settings));
    }

    /**
     * @param string $templateType
     * @param string $templateTechnology
     * @return array
     */
    private function getGlobalSettingsWithDefaultValuesList($templateType, $templateTechnology)
    {
        if (null === $this->globalSettingsWithDefaultValues) {
            $this->globalSettingsWithDefaultValues = [];
            $settingsList = [];
            foreach ($this->getSettingsRegistry()->getSettings($templateType, $templateTechnology) as $setting) {
                foreach ($setting->getPaths() as $path) {
                    $event = new SettingContainingEvent($setting);
                    $this->getEventDispatcher()->dispatch($event, sprintf('builderius_setting_convert_%s', $setting->getName()));
                    $settingsList[$path->getForm()->getName()][$path->getTab()->getName()][$path->getCategory()->getName()][] =
                        $this->getSettingToArrayConverter()->convert($event->getSetting());
                }
                foreach ($setting->getFacades() as $facade) {
                    foreach ($facade->getPaths() as $path) {
                        $settingsList[$path->getForm()->getName()][$path->getTab()->getName()][$path->getCategory()->getName()][] =
                            BuilderiusSettingFacadeToArrayConverter::convert($facade);
                    }
                }
            }
            if (isset($settingsList['global']) && is_array($settingsList['global'])) {
                foreach ($settingsList['global'] as $settingsByCategories) {
                    foreach ($settingsByCategories as $settings) {
                        foreach ($settings as $settingConfig) {
                            if (isset($settingConfig['name']) && is_array($settingConfig['value']) && !empty($settingConfig['value'])) {
                                $this->globalSettingsWithDefaultValues[$settingConfig['name']] = $settingConfig['value'];
                            }
                        }
                    }
                }
            }
        }

        return $this->globalSettingsWithDefaultValues;
    }

    /**
     * @return BuilderiusTemplateConfigVersionConverterInterface[]
     */
    private function getBuilderiusTemplateConfigVersionConverters()
    {
        /** @var CompositeBuilderiusTemplateConfigVersionConverter $compositeConverter */
        $compositeConverter = $this->container->get('builderius_template.version_converter.composite');

        return $compositeConverter->getConverters('builderius', '0.9.4.1');
    }

    /**
     * @return BuilderiusSettingsRegistryInterface
     */
    private function getSettingsRegistry()
    {
        return $this->container->get('builderius_setting.registry.settings');
    }

    /**
     * @return EventDispatcherInterface
     */
    private function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

    /**
     * @return BuilderiusSettingToArrayConverterInterface
     */
    private function getSettingToArrayConverter()
    {
        return $this->container->get('builderius_setting.converter.to_array');
    }
}