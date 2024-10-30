<?php

namespace Builderius\Bundle\TestingBundle\Command;

use Builderius\Bundle\BuilderBundle\Model\BuilderiusBuilderForm;
use Builderius\Bundle\BuilderBundle\Model\BuilderiusBuilderFormTab;
use Builderius\Bundle\CategoryBundle\Model\BuilderiusCategory;
use Builderius\Bundle\GraphQLBundle\Config\GraphQLFieldArgumentConfig;
use Builderius\Bundle\LayoutBundle\Model\BuilderiusLayout;
use Builderius\Bundle\ModuleBundle\Model\AssetAwareBuilderiusContainerModule;
use Builderius\Bundle\ModuleBundle\Model\AssetAwareBuilderiusModule;
use Builderius\Bundle\ModuleBundle\Model\BuilderiusContainerModule;
use Builderius\Bundle\ModuleBundle\Model\BuilderiusModule;
use Builderius\Bundle\SettingBundle\Model\BuilderiusCssSetting;
use Builderius\Bundle\SettingBundle\Model\BuilderiusModuleCssSetting;
use Builderius\Bundle\SettingBundle\Model\BuilderiusModuleSetting;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSetting;
use Builderius\Bundle\TemplateBundle\ApplyRule\BuilderiusTemplateApplyRule;
use Builderius\Bundle\TemplateBundle\ApplyRule\BuilderiusTemplateApplyRuleVariant;
use Builderius\Bundle\TemplateBundle\ApplyRule\Starter\BuilderiusTemplateApplyRuleStarter;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateType;
use Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\AdminMenuPage;
use Builderius\MooMoo\Platform\Bundle\TestingBundle\Kernel\TestKernel;
use Builderius\MooMoo\Platform\Bundle\WpCliBundle\Model\WpCliCommandInterface;


class BuilderiusTempTranslationFilesGenerationCommand implements WpCliCommandInterface
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'builderius:temp-translation-files:generate';
    }

    /**
     * @inheritDoc
     */
    public function execute($arguments = [], $assoc_arguments = [])
    {
        try {
            require __DIR__ . '/../../../../../../../wp-load.php';
            $kernel = new TestKernel('builderius/builderius.php', false);
            $kernel->boot();

            $strings = [];
            foreach ($kernel->getBundles() as $bundle) {
                if ($extension = $bundle->getContainerExtension()) {
                    $containerBuilder = new \Builderius\Symfony\Component\DependencyInjection\ContainerBuilder();
                    $containerBuilder->registerExtension($extension);
                    $extension->load([], $containerBuilder);
                    $pluginName = $bundle->getPluginName();
                    if (!isset($strings[$pluginName])) {
                        $strings[$pluginName] = [];
                    }
                    foreach ($containerBuilder->getDefinitions() as $definition) {
                        if (in_array($definition->getClass(), [
                            GraphQLFieldArgumentConfig::class
                        ])) {
                            foreach($definition->getArguments() as $arguments) {
                                if (isset($arguments['description']) && !in_array($arguments['description'], $strings[$pluginName])) {
                                    $strings[$pluginName][] = $arguments['description'];
                                }
                            }
                        } elseif (in_array($definition->getClass(), [
                            BuilderiusTemplateApplyRuleStarter::class
                        ])) {
                            foreach($definition->getArguments() as $arguments) {
                                if (isset($arguments['title']) && !in_array($arguments['title'], $strings[$pluginName])) {
                                    $strings[$pluginName][] = $arguments['title'];
                                }
                            }
                        } elseif (in_array($definition->getClass(), [
                            AdminMenuPage::class
                        ])) {
                            foreach($definition->getArguments() as $arguments) {
                                if (isset($arguments['page_title']) && !in_array($arguments['page_title'], $strings[$pluginName])) {
                                    $strings[$pluginName][] = $arguments['page_title'];
                                }
                                if (isset($arguments['menu_title']) && !in_array($arguments['menu_title'], $strings[$pluginName])) {
                                    $strings[$pluginName][] = $arguments['menu_title'];
                                }
                            }
                        } elseif (in_array($definition->getClass(), [
                            BuilderiusCategory::class,
                            BuilderiusBuilderFormTab::class,
                            BuilderiusBuilderForm::class,
                            BuilderiusModule::class,
                            BuilderiusContainerModule::class,
                            AssetAwareBuilderiusContainerModule::class,
                            AssetAwareBuilderiusModule::class,
                            BuilderiusLayout::class,
                            BuilderiusTemplateApplyRule::class,
                            BuilderiusTemplateApplyRuleVariant::class,
                            BuilderiusTemplateType::class,
                            BuilderiusCssSetting::class,
                            BuilderiusSetting::class,
                            BuilderiusModuleSetting::class,
                            BuilderiusModuleCssSetting::class,
                        ])) {
                            foreach($definition->getArguments() as $arguments) {
                                if (isset($arguments['label']) && !in_array($arguments['label'], $strings[$pluginName])) {
                                    $strings[$pluginName][] = $arguments['label'];
                                }
                            }
                        }
                    }
                }
            }
            foreach ($strings as $plugin => $strByPlugin) {
                asort($strByPlugin);
                $strings[$plugin] = array_values($strByPlugin);
            }
            foreach ($strings as $plugin => $strByPlugin) {
                $formattedName = explode('/', $plugin)[0];
                $translString = "<?php\n";
                foreach ($strByPlugin as $phrase) {
                    $translString .= sprintf("__('%s', 'builderius');\n", $phrase);
                }
                file_put_contents(sprintf("%s/%s/src/Bundle/TestingBundle/YmlTranslations.php", WP_PLUGIN_DIR, $formattedName), $translString);
            }

            \WP_CLI::line('Temp translation files were successfully generated');
        } catch(\Exception $e) {
            \WP_CLI::line($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalRegistrationParameters()
    {
        return [
            'shortdesc' => 'Generates temp translation files'
        ];
    }
}
