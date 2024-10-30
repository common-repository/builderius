<?php

namespace Builderius\Bundle\TemplateBundle;

use Builderius\Bundle\TemplateBundle\DependencyInjection\CompilerPass\ApplyRuleChildrenCompilerPass;
use Builderius\Bundle\TemplateBundle\DependencyInjection\CompilerPass\ApplyRuleVariantsCompilerPass;
use Builderius\Bundle\TemplateBundle\DependencyInjection\CompilerPass\TwigRenderingSymbolsChangeCompilerPass;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Bundle\Bundle;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\DependencyInjection\CompilerPass\KernelCompilerPass;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;

class BuilderiusTemplateBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TwigRenderingSymbolsChangeCompilerPass());
        $container->addCompilerPass(new ApplyRuleVariantsCompilerPass());
        $container->addCompilerPass(new ApplyRuleChildrenCompilerPass());
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_content_provider',
                'builderius_template.provider.template_content.composite',
                'addProvider'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_type',
                'builderius_template.provider.template_types',
                'addType'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_acceptable_hooks_provider',
                'builderius_template.provider.template_acceptable_hooks',
                'addProvider'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_acceptable_core_hook',
                'builderius_template.provider.template_acceptable_hooks.wp_core',
                'addHook'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_acceptable_generatepress_hook',
                'builderius_template.provider.template_acceptable_hooks.generatepress',
                'addHook'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_acceptable_kadence_hook',
                'builderius_template.provider.template_acceptable_hooks.kadence',
                'addHook'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_acceptable_astra_hook',
                'builderius_template.provider.template_acceptable_hooks.astra',
                'addHook'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_acceptable_blocksy_hook',
                'builderius_template.provider.template_acceptable_hooks.blocksy',
                'addHook'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_acceptable_blocksy_wc_hook',
                'builderius_template.provider.template_acceptable_hooks.blocksy_wc',
                'addHook'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_sub_type',
                'builderius_template.provider.template_sub_types',
                'addSubType'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_apply_rule',
                'builderius_template.registry.apply_rules',
                'addRule'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_apply_rule_category',
                'builderius_template.registry.apply_rule_categories',
                'addCategory'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_apply_rule_starter',
                'builderius_template.registry.apply_rule_starters',
                'addStarter'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_rule_applicants_provider',
                'builderius_template.provider.rule_applicants.composite',
                'addProvider'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_applicant_data_provider',
                'builderius_template.provider.applicant_data.composite',
                'addProvider'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_rule_applicant_parameters_provider',
                'builderius_template.provider.rule_applicant_parameters.composite',
                'addProvider'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_config_version_converter',
                'builderius_template.version_converter.composite',
                'addConverter'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_data_var_value_generator',
                'builderius_template.generator.data_var_value.composite',
                'addGenerator'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_dynamic_data_helpers_provider',
                'builderius_template.provider.dynamic_data_helpers.composite',
                'addProvider'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_dynamic_data_helper',
                'builderius_template.provider.dynamic_data_helpers.base',
                'addHelper'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_dynamic_data_helpers_category',
                'builderius_template.provider.dynamic_data_helpers_categories',
                'addCategory'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_template_applicant_category',
                'builderius_template.provider.template_applicant_categories',
                'addCategory'
            )
        );
        $container->addCompilerPass(
            new KernelCompilerPass(
                'builderius_available_singulars_provider_id',
                'builderius_template.apply_rule.arguments_provider.available_singulars.id',
                'addProvider'
            )
        );
    }
}
