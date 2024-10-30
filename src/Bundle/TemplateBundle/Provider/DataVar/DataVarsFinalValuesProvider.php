<?php

namespace Builderius\Bundle\TemplateBundle\Provider\DataVar;

use Builderius\Bundle\DeliverableBundle\Provider\BuilderiusDeliverableProviderInterface;
use Builderius\Bundle\SettingBundle\Factory\BuilderiusGlobalSettingsSetFromPostFactory;
use Builderius\Bundle\SettingBundle\Registration\BuilderiusGlobalSettingsSetPostType;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateDataVarsContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModuleProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionInterface;

class DataVarsFinalValuesProvider implements DataVarsFinalValuesProviderInterface
{
    /**
     * @var DataVarValueGeneratorInterface
     */
    private $dataVarValueGenerator;

    /**
     * @var BuilderiusTemplateProviderInterface
     */
    private $templateProvider;
    /**
     * @var BuilderiusDeliverableProviderInterface
     */
    private $deliverableProvider;

    /**
     * @var DeliverableTemplateSubModuleProviderInterface
     */
    private $dtsmProvider;

    /**
     * @var ConditionInterface[]
     */
    private $previewConditions;

    /**
     * @var ConditionInterface[]
     */
    private $frontendConditions;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusGlobalSettingsSetFromPostFactory
     */
    protected $globalSettingsSetFromPostFactory;

    /**
     * @param DataVarValueGeneratorInterface $dataVarValueGenerator
     * @param BuilderiusTemplateProviderInterface $templateProvider
     * @param BuilderiusDeliverableProviderInterface $deliverableProvider
     * @param DeliverableTemplateSubModuleProviderInterface $dtsmProvider
     * @param ConditionInterface[] $previewConditions
     * @param ConditionInterface[] $frontendConditions
     * @param BuilderiusRuntimeObjectCache $cache
     * @param \WP_Query $wpQuery
     * @param BuilderiusGlobalSettingsSetFromPostFactory $globalSettingsSetFromPostFactory
     */
    public function __construct(
        DataVarValueGeneratorInterface $dataVarValueGenerator,
        BuilderiusTemplateProviderInterface $templateProvider,
        BuilderiusDeliverableProviderInterface $deliverableProvider,
        DeliverableTemplateSubModuleProviderInterface $dtsmProvider,
        array $previewConditions,
        array $frontendConditions,
        BuilderiusRuntimeObjectCache $cache,
        \WP_Query $wpQuery,
        BuilderiusGlobalSettingsSetFromPostFactory $globalSettingsSetFromPostFactory
    ) {
        $this->dataVarValueGenerator = $dataVarValueGenerator;
        $this->templateProvider = $templateProvider;
        $this->deliverableProvider = $deliverableProvider;
        $this->dtsmProvider = $dtsmProvider;
        $this->previewConditions = $previewConditions;
        $this->frontendConditions = $frontendConditions;
        $this->cache = $cache;
        $this->wpQuery = $wpQuery;
        $this->globalSettingsSetFromPostFactory = $globalSettingsSetFromPostFactory;
    }

    /**
     * @return bool
     */
    private function isPreview()
    {
        foreach ($this->previewConditions as $previewCondition) {
            if (!$previewCondition->evaluate()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    private function isFrontend()
    {
        foreach ($this->frontendConditions as $frontendCondition) {
            if (!$frontendCondition->evaluate()) {
                return false;
            }
        }

        return true;
    }
    /**
     * @return array
     */
    public function getDataVarsFinalConfigs()
    {
        $dataVarsConfigs = [];
        if ($this->isPreview()) {
            $template = $this->cache->get('builderius_hook_template');
            if (!$template) {
                $template = $this->templateProvider->getTemplate();
            }
            if ($template) {
                $dataVarsConfigs = $this->cache->get(sprintf('builderius_data_vars_final_configs_%s', $template->getId()));
                if (false === $dataVarsConfigs) {
                    $technologyName = $template->getTechnology();
                    $posts = $this->cache->get(sprintf('builderius_gss_posts_%s', $technologyName));
                    if (false == $posts) {
                        $queryArgs = [
                            'post_type' => BuilderiusGlobalSettingsSetPostType::POST_TYPE,
                            'post_status' => get_post_stati(),
                            'name' => $technologyName,
                            'posts_per_page' => -1,
                            'no_found_rows' => true,
                            'orderby' => 'name',
                            'order' => 'ASC'
                        ];
                        $posts = $this->wpQuery->query($queryArgs);
                        $this->cache->set(sprintf('builderius_gss_posts_%s', $technologyName), $posts);
                    }
                    foreach ($posts as $post) {
                        $globalSettingsSet = $this->globalSettingsSetFromPostFactory->createGlobalSettingsSet($post);
                        $branch = $globalSettingsSet->getActiveBranch();
                        $dataVarsContent = [];
                        if ($branch) {
                            if ($branch->getContent(BuilderiusTemplateDataVarsContentProvider::CONTENT_TYPE)) {
                                $dataVarsContent = $branch->getContent(BuilderiusTemplateDataVarsContentProvider::CONTENT_TYPE);
                            } else {
                                $commit = $branch->getActiveCommit();
                                if ($commit) {
                                    if ($commit->getContent(BuilderiusTemplateDataVarsContentProvider::CONTENT_TYPE)) {
                                        $dataVarsContent = $commit->getContent(BuilderiusTemplateDataVarsContentProvider::CONTENT_TYPE);
                                    }
                                }
                            }
                        }
                        if (is_array($dataVarsContent)) {
                            if (!is_array($dataVarsConfigs)) {
                                $dataVarsConfigs = [];
                            }
                            foreach ($dataVarsContent as $dataVarsContentItem) {
                                $dataVarsConfigs[$dataVarsContentItem['name']] = $dataVarsContentItem;
                            }
                        }
                    }
                    $templateDataVars = [];
                    $branch = $template->getActiveBranch();
                    if ($branch) {
                        $commit = $branch->getActiveCommit();
                        if ($commit) {
                            $templateDataVars = $commit->getContent(BuilderiusTemplateDataVarsContentProvider::CONTENT_TYPE);
                        } else {
                            $templateDataVars = $branch->getContent(BuilderiusTemplateDataVarsContentProvider::CONTENT_TYPE);
                        }
                    }
                    if (is_array($templateDataVars)) {
                        foreach ($templateDataVars as $templateDataVarsItem) {
                            $dataVarsConfigs[$templateDataVarsItem['name']] = $templateDataVarsItem;
                        }
                    }
                }
                $this->cache->set(sprintf('builderius_data_vars_final_configs_%s', $template->getId()), $dataVarsConfigs);
            }
        } elseif ($this->isFrontend()) {
            $templateSubModule = $this->cache->get('builderius_dtsm_hook_template');
            if (!$templateSubModule) {
                $templateSubModule = $this->dtsmProvider->getTemplateSubModule();
            }
            if ($templateSubModule) {
                $dataVarsConfigs = $this->cache->get('builderius_data_vars_final_configs');
                if (false === $dataVarsConfigs) {
                    $dataVarsConfigs = [];
                    $technology = $templateSubModule->getTechnology();
                    $deliverable = $this->deliverableProvider->getDeliverable();
                    $gssAll = $deliverable->getSubModules('global_settings_set', $technology);
                    if (!empty($gssAll)) {
                        $gssAll = reset($gssAll);
                        $gssAllDataVars = $gssAll->getContent(BuilderiusTemplateDataVarsContentProvider::CONTENT_TYPE) ?: [];
                        foreach ($gssAllDataVars as $gssAllDataVarsItem) {
                            $dataVarsConfigs[$gssAllDataVarsItem['name']] = $gssAllDataVarsItem;
                        }
                    }
                    $templateDataVars = $templateSubModule->getContent(BuilderiusTemplateDataVarsContentProvider::CONTENT_TYPE) ?: [];
                    foreach ($templateDataVars as $templateDataVarsItem) {
                        $dataVarsConfigs[$templateDataVarsItem['name']] = $templateDataVarsItem;
                    }
                }
            }
        }

        return $dataVarsConfigs;
    }

    /**
     * @inheritDoc
     */
    public function getDataVarsFinalValues()
    {
        $dataVarsValues = [];
        if ($this->isPreview()) {
            $template = $this->cache->get('builderius_hook_template');
            if (!$template) {
                $template = $this->templateProvider->getTemplate();
            }
            if ($template) {
                $gettingHookArgsBeforeHook = $this->cache->get('getting_hook_args_before_hook');
                if (false === $gettingHookArgsBeforeHook) {
                    $hookTemplateResolving = $this->cache->get('hook_template_resolving');
                    if (false === $hookTemplateResolving) {
                        $dataVarsValues = $this->cache->get(sprintf('builderius_data_vars_values_%s', $template->getId()));
                    } else {
                        $index = $this->cache->get(sprintf('hook_template_%d_index', $template->getId()));
                        if (false === $index) {
                            $index = 0;
                        }
                        $dataVarsValues = $this->cache->get(sprintf('builderius_data_vars_values_with_hook_args_%s_%s', $template->getId(), $index));
                    }
                } else {
                    $dataVarsValues = $this->cache->get(sprintf('builderius_data_vars_values_with_hook_args_before_hook_%s', $template->getId()));
                }
                $dataVarsConfigs = $this->getDataVarsFinalConfigs();
                if (false === $dataVarsValues || (is_array($dataVarsValues) && count($dataVarsValues) !== count($dataVarsConfigs))) {
                    if (false === $dataVarsValues) {
                        $dataVarsValues = [];
                    }
                    $type = $template->getType();
                    foreach (array_keys($dataVarsConfigs) as $name) {
                        $dataVarsValues = $this->dataVarValueGenerator->generateValue(
                            $type,
                            $name,
                            $dataVarsConfigs,
                            $dataVarsValues
                        );
                    }
                    $gettingHookArgsBeforeHook = $this->cache->get('getting_hook_args_before_hook');
                    if (false === $gettingHookArgsBeforeHook) {
                        $hookTemplateResolving = $this->cache->get('hook_template_resolving');
                        if (false === $hookTemplateResolving) {
                            $this->cache->set(sprintf('builderius_data_vars_values_%s', $template->getId()), $dataVarsValues);
                        } else {
                            $index = $this->cache->get(sprintf('hook_template_%d_index', $template->getId()));
                            if (false === $index) {
                                $index = 0;
                            }
                            $this->cache->set(sprintf('builderius_data_vars_values_with_hook_args_%s_%s', $template->getId(), $index), $dataVarsValues);
                        }
                    } else {
                        $this->cache->set(sprintf('builderius_data_vars_values_with_hook_args_before_hook_%s', $template->getId()), $dataVarsValues);
                    }
                }
            }
        } elseif ($this->isFrontend()) {
            $templateSubModule = $this->cache->get('builderius_dtsm_hook_template');
            if (!$templateSubModule) {
                $templateSubModule = $this->dtsmProvider->getTemplateSubModule();
            }
            if ($templateSubModule) {
                $gettingHookArgsBeforeHook = $this->cache->get('getting_hook_args_before_hook');
                if (false === $gettingHookArgsBeforeHook) {
                    $hookTemplateResolving = $this->cache->get('hook_template_resolving');
                    if (false === $hookTemplateResolving) {
                        $dataVarsValues = $this->cache->get(sprintf('builderius_data_vars_values_%s', $templateSubModule->getId()));
                    } else {
                        $index = $this->cache->get(sprintf('dtsm_hook_template_%d_index', $templateSubModule->getId()));
                        if (false === $index) {
                            $index = 0;
                        }
                        $dataVarsValues = $this->cache->get(sprintf('builderius_data_vars_values_with_hook_args_%s_%s', $templateSubModule->getId(), $index));
                    }
                } else {
                    $dataVarsValues = $this->cache->get(sprintf('builderius_data_vars_values_with_hook_args_before_hook_%s', $templateSubModule->getId()));
                }
                $dataVarsConfigs = $this->getDataVarsFinalConfigs();
                if (false === $dataVarsValues || (is_array($dataVarsValues) && count($dataVarsValues) !== count($dataVarsConfigs))) {
                    if (false === $dataVarsValues) {
                        $dataVarsValues = [];
                    }
                    $type = $templateSubModule->getEntityType();
                    foreach (array_keys($dataVarsConfigs) as $name) {
                        $dataVarsValues = $this->dataVarValueGenerator->generateValue(
                            $type,
                            $name,
                            $dataVarsConfigs,
                            $dataVarsValues
                        );
                    }
                    $gettingHookArgsBeforeHook = $this->cache->get('getting_hook_args_before_hook');
                    if (false === $gettingHookArgsBeforeHook) {
                        $hookTemplateResolving = $this->cache->get('hook_template_resolving');
                        if (false === $hookTemplateResolving) {
                            $this->cache->set(sprintf('builderius_data_vars_values_%s', $templateSubModule->getId()), $dataVarsValues);
                        } else {
                            $index = $this->cache->get(sprintf('dtsm_hook_template_%d_index', $templateSubModule->getId()));
                            if (false === $index) {
                                $index = 0;
                            }
                            $this->cache->set(sprintf('builderius_data_vars_values_with_hook_args_%s_%s', $templateSubModule->getId(), $index), $dataVarsValues);
                        }
                    } else {
                        $this->cache->set(sprintf('builderius_data_vars_values_with_hook_args_before_hook_%s', $templateSubModule->getId()), $dataVarsValues);
                    }
                }
            }
        }

        return $dataVarsValues;
    }

    /**
     * @inheritDoc
     */
    public function getDataVarFinalValue($dataVarName)
    {
        $dataVarsValues = $this->getDataVarsFinalValues();

        return array_key_exists($dataVarName, $dataVarsValues) ? $dataVarsValues[$dataVarName] : null;
    }
}