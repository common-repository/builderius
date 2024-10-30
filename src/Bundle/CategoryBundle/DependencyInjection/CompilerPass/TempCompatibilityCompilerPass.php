<?php

namespace Builderius\Bundle\CategoryBundle\DependencyInjection\CompilerPass;

use Builderius\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;

class TempCompatibilityCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        //types
        $types = $container->findTaggedServiceIds('builderius_template_type');
        if (!$types) {
            return;
        }
        foreach ($types as $type => $attributes) {
            $typeDefinition = $container->getDefinition($type);
            $arguments = $typeDefinition->getArguments()[0];
            if (in_array($arguments['name'], ['collection', 'other'])) {
                $container->removeDefinition($type);
            }
        }

        //apply rules
        $applyRules = $container->findTaggedServiceIds('builderius_template_apply_rule');
        if (!$applyRules) {
            return;
        }
        foreach ($applyRules as $applyRule => $attributes) {
            $applyRuleDefinition = $container->getDefinition($applyRule);
            $arguments = $applyRuleDefinition->getArguments()[0];
            if (isset($arguments['templateTypes'])) {
                $tmplTypes = ['template'];
                foreach ($arguments['templateTypes'] as $tmplType) {
                    if (!in_array($tmplType, ['singular', 'template', 'collection', 'other']) && !in_array($tmplType, $tmplTypes)) {
                        $tmplTypes[] = $tmplType;
                    }
                }
                $arguments['templateTypes'] = $tmplTypes;
                $applyRuleDefinition->setArgument(0, $arguments);
            }
        }

        // starters
        $starters = $container->findTaggedServiceIds('builderius_template_apply_rule_starter');
        if (!$starters) {
            return;
        }
        foreach ($starters as $starter => $attributes) {
            $starterDefinition = $container->getDefinition($starter);
            $arguments = $starterDefinition->getArguments();
            if (isset($arguments[0])) {
                $arguments = $arguments[0];
                if (is_array($arguments) && isset($arguments['template_types'])) {
                    $tmplTypes = ['template'];
                    foreach ($arguments['template_types'] as $tmplType) {
                        if (!in_array($tmplType, ['singular', 'template', 'collection', 'other']) && !in_array($tmplType, $tmplTypes)) {
                            $tmplTypes[] = $tmplType;
                        }
                    }
                    $arguments['template_types'] = $tmplTypes;
                    $starterDefinition->setArgument(0, $arguments);
                }
            }
        }

        // settings
        $settings = $container->findTaggedServiceIds('builderius_setting');
        if (!$settings) {
            return;
        }
        foreach ($settings as $setting => $attributes) {
            $settingDefinition = $container->getDefinition($setting);
            $arguments = $settingDefinition->getArguments();
            if (isset($arguments[0])) {
                $arguments = $arguments[0];
                if (is_array($arguments) && isset($arguments['appliedToTemplateTypes'])) {
                    $tmplTypes = ['template'];
                    foreach ($arguments['appliedToTemplateTypes'] as $tmplType) {
                        if (!in_array($tmplType, ['singular', 'template', 'collection', 'other']) && !in_array($tmplType, $tmplTypes)) {
                            $tmplTypes[] = $tmplType;
                        }
                    }
                    $arguments['appliedToTemplateTypes'] = $tmplTypes;
                    $settingDefinition->setArgument(0, $arguments);
                }
            }
        }

        //settings decorators
        $settingsDecorationClasses = [
            'Builderius\Bundle\SettingBundle\Model\BuilderiusModuleSettingDecorator',
            'Builderius\Bundle\SettingBundle\Model\BuilderiusModuleCssSettingDecorator'
        ];
        foreach ($container->getDefinitions() as $definition) {
            if (in_array($definition->getClass(), $settingsDecorationClasses)) {
                if ($definition->hasMethodCall('setAppliedToTemplateTypes')) {
                    $definition->removeMethodCall('setAppliedToTemplateTypes');
                    $definition->addMethodCall('setAppliedToTemplateTypes', [['template', 'template_part']]);
                }
            }
        }

        // modules
        $modules = $container->findTaggedServiceIds('builderius_module');
        if (!$modules) {
            return;
        }
        foreach ($modules as $module => $attributes) {
            $moduleDefinition = $container->getDefinition($module);
            $arguments = $moduleDefinition->getArguments();
            if (isset($arguments[0])) {
                $arguments = $arguments[0];
                if (is_array($arguments) && isset($arguments['templateTypes'])) {
                    $tmplTypes = ['template'];
                    foreach ($arguments['templateTypes'] as $tmplType) {
                        if (!in_array($tmplType, ['singular', 'template', 'collection', 'other']) && !in_array($tmplType, $tmplTypes)) {
                            $tmplTypes[] = $tmplType;
                        }
                    }
                    $arguments['templateTypes'] = $tmplTypes;
                    $moduleDefinition->setArgument(0, $arguments);
                }
            }
        }

        //modules decorators
        $modulesDecorationClasses = [
            'Builderius\Bundle\ModuleBundle\Model\BuilderiusModuleDecorator',
            'Builderius\Bundle\ModuleBundle\Model\BuilderiusContainerModuleDecorator',
            'Builderius\Bundle\ModuleBundle\Model\AssetAwareBuilderiusModuleDecorator',
            'Builderius\Bundle\ModuleBundle\Model\AssetAwareBuilderiusContainerModuleDecorator'
        ];
        foreach ($container->getDefinitions() as $definition) {
            if (in_array($definition->getClass(), $modulesDecorationClasses)) {
                if ($definition->hasMethodCall('setTemplateTypes')) {
                    $definition->removeMethodCall('setTemplateTypes');
                    $definition->addMethodCall('setTemplateTypes', [['template', 'template_part']]);
                }
            }
        }

        //graphql resolvers
        $resolvers = $container->findTaggedServiceIds('builderius_graphql_field_resolver');
        if (!$resolvers) {
            return;
        }
        foreach ($resolvers as $resolver => $attributes) {
            $resolverDefinition = $container->getDefinition($resolver);
            $arguments = $resolverDefinition->getArguments();
            if (isset($arguments[0]) && is_array($arguments[0])) {
                $arguments = $arguments[0];
                if (in_array('Singular', $arguments) || in_array('Collection', $arguments) || in_array('Other', $arguments)) {
                    if (($key = array_search('Collection', $arguments)) !== false) {
                        unset($arguments[$key]);
                    }
                    if (($key = array_search('Other', $arguments)) !== false) {
                        unset($arguments[$key]);
                    }
                    if (($key = array_search('Singular', $arguments)) !== false) {
                        $arguments[$key] = 'Template';
                    }
                    $arguments = array_values($arguments);
                    $resolverDefinition->setArgument(0, $arguments);
                }
            }
        }
    }
}
