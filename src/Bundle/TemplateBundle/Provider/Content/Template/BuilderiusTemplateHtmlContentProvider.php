<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Content\Template;

use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\SettingBundle\Factory\SettingValue\BuilderiusSettingValueFactoryInterface;
use Builderius\Bundle\SettingBundle\Generator\FinalSettingValue\FinalSettingValueGeneratorInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingCssAwareInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValue;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;
use Builderius\Symfony\Component\Templating\EngineInterface;

class BuilderiusTemplateHtmlContentProvider extends AbstractBuilderiusTemplateContentProvider
{
    const CONTENT_TYPE = 'html';

    /**
     * @var BuilderiusSettingValueFactoryInterface
     */
    private $settingValueFactory;

    /**
     * @var BuilderiusModulesProviderInterface
     */
    private $modulesProvider;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @var FinalSettingValueGeneratorInterface
     */
    private $finalSettingValueGenerator;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param BuilderiusSettingValueFactoryInterface $settingValueFactory
     * @param BuilderiusModulesProviderInterface $modulesProvider
     * @param EngineInterface $templatingEngine
     * @param FinalSettingValueGeneratorInterface $finalSettingValueGenerator
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        BuilderiusSettingValueFactoryInterface $settingValueFactory,
        BuilderiusModulesProviderInterface $modulesProvider,
        EngineInterface $templatingEngine,
        FinalSettingValueGeneratorInterface $finalSettingValueGenerator,
        EventDispatcher $eventDispatcher
    ) {
        $this->settingValueFactory = $settingValueFactory;
        $this->modulesProvider = $modulesProvider;
        $this->templatingEngine = $templatingEngine;
        $this->finalSettingValueGenerator = $finalSettingValueGenerator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function getContentType()
    {
        return self::CONTENT_TYPE;
    }

    /**
     * @inheritDoc
     */
    public function getContent($technology, array $contentConfig)
    {
        if (!in_array($technology, $this->technologies)) {
            return null;
        }
        $hierarchicalConfig =
            ConfigToHierarchicalConfigConverter::convert($contentConfig);
        $htmlParts = [];
        $templateConfig = isset($contentConfig['template']) ? $contentConfig['template'] : [];
        foreach ($hierarchicalConfig as $moduleConfig) {
            $htmlParts = $this->generateModuleHtml($moduleConfig, $templateConfig, $htmlParts);
        }

        $html = $this->templatingEngine->render(
            'BuilderiusTemplateBundle:templateHtml.twig',
            [
                'parts' => $htmlParts
            ]
        );
        preg_match_all('/\[\[\[(.*?)\]\]\]/s', $html, $nonEscapedDataVars);
        $nonEscapedDataVarsNames = array_unique($nonEscapedDataVars[1]);

        foreach ($nonEscapedDataVarsNames as $nonEscapedDataVarName) {
            $countOpen = substr_count($nonEscapedDataVarName,'[');
            $countClose = substr_count($nonEscapedDataVarName,']');
            $diff = $countOpen - $countClose;
            if ($diff > 0) {
                $prefix = ']';
                for ($i = 1; $i < $diff; $i++) {
                    $prefix = $prefix . ']';
                }
                $nonEscapedDataVarName = $nonEscapedDataVarName . $prefix;
            }
            $html = str_replace(
                sprintf("[[[%s]]]", $nonEscapedDataVarName),
                sprintf("[^builderius_data_var('%s')|raw^]", str_replace("'", "\'", trim($nonEscapedDataVarName))),
                $html
            );
        }
        preg_match_all('/\[\[(.*?)\]\]/s', $html, $escapedDataVars);
        $escapedDataVarsNames = array_unique($escapedDataVars[1]);

        foreach ($escapedDataVarsNames as $escapedDataVarName) {
            $countOpen = substr_count($escapedDataVarName,'[');
            $countClose = substr_count($escapedDataVarName,']');
            $diff = $countOpen - $countClose;
            if ($diff > 0) {
                $prefix = ']';
                for ($i = 1; $i < $diff; $i++) {
                    $prefix = $prefix . ']';
                }
                $escapedDataVarName = $escapedDataVarName . $prefix;
            }
            $html = str_replace(
                sprintf("[[%s]]", $escapedDataVarName),
                sprintf("[^builderius_data_var_escaped('%s')|raw^]", str_replace("'", "\'", trim($escapedDataVarName))),
                $html
            );
        }

        return $html;
    }

    /**
     * @param array $moduleConfig
     * @param array $templateConfig
     * @param array $htmlParts
     * @return array
     * @throws \Exception
     */
    protected function generateModuleHtml(array $moduleConfig, array $templateConfig, array $htmlParts)
    {
        $childrenHtml = [];
        if (isset($moduleConfig['children'])) {
            foreach ($moduleConfig['children'] as $childrenModuleConfig) {
                $childrenHtml = $this->generateModuleHtml($childrenModuleConfig, $templateConfig, $childrenHtml);
            }
        }

        $module = $this->modulesProvider->getModule(
            $moduleConfig['name'],
            $templateConfig['type'],
            $templateConfig['technology']
        );
        $moduleSettingsValues = [];
        foreach ($moduleConfig['settings'] as $settingData) {
            $setting = $module->getSetting($settingData['name']);
            if ($setting) {
                if (!$setting instanceof BuilderiusSettingCssAwareInterface &&
                    $setting->getContentType() === self::CONTENT_TYPE) {
                    $settingValue = $this->settingValueFactory->create([
                        BuilderiusSettingCssAwareInterface::CSS_FIELD => false,
                        BuilderiusSettingValue::VALUE_FIELD => $settingData['value']
                    ]);
                    $setting->addValue($settingValue);
                    $settingValues = $setting->getValues();
                    $settingValue = reset($settingValues);
                    $valueExpressions = $setting->getValueExpressions();
                    foreach ($valueExpressions as $valueExpression) {
                        $moduleSettingsValues[$valueExpression->getName()] =
                            $this->finalSettingValueGenerator->generateFinalSettingValue(
                                $settingValue,
                                $valueExpression,
                                $setting->getValueSchema()
                            );
                    }
                }
                $setting->resetValues();
            }
        }

        $htmlConfig = $moduleSettingsValues;
        $htmlConfig['id'] = $moduleConfig['id'];
        if (!empty($childrenHtml)) {
            $htmlConfig['children'] = $childrenHtml;
        }
        $fullConfig = [
            'module' => $module,
            'htmlConfig' => $htmlConfig,
            'settings' => $moduleConfig['settings']
        ];
        $event = new ConfigContainingEvent($fullConfig);
        $this->eventDispatcher->dispatch($event, 'builderius_html_config_before_render');
        $fullConfig = $event->getConfig();
        $htmlConfig = isset($fullConfig['htmlConfig']) ? $fullConfig['htmlConfig'] : [];
        $htmlParts[] = $this->templatingEngine->render($module->getHtmlTemplate(), $htmlConfig);

        return $htmlParts;
    }
}
