<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\ModuleBundle\Model\BuilderiusModule;
use Builderius\Bundle\TemplateBundle\Converter\ConfigToHierarchicalConfigConverter;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;

class BuilderiusTemplateClassesScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'classes';
    const MODULES_CLASSES = 'modulesClasses';
    const UNIQUE_CLASSES = 'uniqueClasses';

    /**
     * @var BuilderiusTemplateProviderInterface
     */
    private $templateProvider;

    /**
     * @param BuilderiusTemplateProviderInterface $templateProvider
     */
    public function __construct(BuilderiusTemplateProviderInterface $templateProvider)
    {
        $this->templateProvider = $templateProvider;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $template = $this->templateProvider->getTemplate();
        if ($template) {
            $activeBranch = $template->getActiveBranch();
            if ($activeBranch) {
                $activeCommit = $activeBranch->getActiveCommit();
                if ($activeCommit) {
                    $activeConfig = $activeCommit->getContentConfig();
                } else {
                    $activeConfig = $activeBranch->getNotCommittedConfig();
                }
                $modulesKey = ConfigToHierarchicalConfigConverter::CONFIG_MODULES_KEY;
                if (is_array($activeConfig) && isset($activeConfig[$modulesKey])) {
                    $finalClasses = [];
                    $finalClasses[self::MODULES_CLASSES] = [];
                    $finalClasses[self::UNIQUE_CLASSES] = [];
                    foreach ($activeConfig[$modulesKey] as $moduleId => $moduleConfig) {
                        foreach ($moduleConfig[BuilderiusModule::SETTINGS_FIELD] as $setting) {
                            if ($setting[BuilderiusTemplate::NAME_FIELD] === 'tagClass') {
                                $classes = $setting['value']['a1'];
                                $finalClasses[self::MODULES_CLASSES][$moduleId] = $classes;
                                foreach ($classes as $class) {
                                    if (!in_array($class, $finalClasses)) {
                                        $finalClasses[self::UNIQUE_CLASSES][] = $class;
                                    }
                                }
                                break;
                            }
                        }
                    }

                    return $finalClasses;
                }
            }
        }

        return [];
    }
}
