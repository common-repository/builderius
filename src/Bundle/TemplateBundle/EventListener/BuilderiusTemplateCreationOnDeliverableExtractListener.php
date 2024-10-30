<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\DeliverableBundle\Event\DeliverableContainingEvent;
use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;

class BuilderiusTemplateCreationOnDeliverableExtractListener
{
    /**
     * @var BuilderiusEntitiesGraphQLQueriesExecutorInterface
     */
    private $graphQLQueriesExecutor;

    /**
     * @var  BuilderiusTemplateConfigVersionConverterInterface
     */
    private $configVersionConverter;

    /**
     * @param BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor
     * @param BuilderiusTemplateConfigVersionConverterInterface $configVersionConverter
     */
    public function __construct(
        BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor,
        BuilderiusTemplateConfigVersionConverterInterface $configVersionConverter
    ) {
        $this->graphQLQueriesExecutor = $graphQLQueriesExecutor;
        $this->configVersionConverter = $configVersionConverter;
    }

    /**
     * @param DeliverableContainingEvent $event
     */
    public function onDeliverableExtraction(DeliverableContainingEvent $event)
    {
        $deliverable = $event->getDeliverable();
        foreach ($deliverable->getSubModules('template') as $dsm) {
            $attributes = $dsm->getAttributes();
            if (in_array($dsm->getType(), ['regular', 'singular', 'collection', 'other'])) {
                $this->createRegularTemplatePost(
                    $dsm->getName(),
                    $dsm->getTechnology(),
                    $dsm->getContentConfig(),
                    $attributes[BuilderiusTemplate::SORT_ORDER_FIELD],
                    $attributes[BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD],
                );
            } elseif ($dsm->getType() === 'hook') {
                $this->createHookTemplatePost(
                    $dsm->getName(),
                    $dsm->getTechnology(),
                    $dsm->getContentConfig(),
                    $attributes[BuilderiusTemplate::SORT_ORDER_FIELD],
                    $attributes[BuilderiusTemplate::HOOK_FIELD],
                    $attributes[BuilderiusTemplate::HOOK_TYPE_FIELD],
                    $attributes[BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD],
                    $attributes[BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD],
                    $attributes[BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD],
                );
            }
        }
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $technology
     * @param array $config
     * @param int $sortOrder
     * @param array $applyRulesConfig
     * @throws \Exception
     */
    private function createRegularTemplatePost($name, $technology, $config, $sortOrder, $applyRulesConfig)
    {
        $name = str_replace('Template: ', '', $name);
        $name = explode('(tag', $name)[0];
        $serializedApplyRulesConfig = json_encode($applyRulesConfig, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
        $serializedApplyRulesConfig = str_replace('\\', '\\\\', $serializedApplyRulesConfig);
        $serializedApplyRulesConfig = str_replace('"', '\\"', $serializedApplyRulesConfig);
        $mutations = [];
        $mutations[] = [
            'name' => 'createTemplate',
            'query' => 'mutation{
                        createTemplate(input: {
                            title: "' . $name . '",
                            type: "template",
                            sub_type: "regular",
                            technology: "' . $technology . '",
                            sort_order: ' . $sortOrder . ',
                            serialized_apply_rules_config: "' . $serializedApplyRulesConfig . '"
                        }){
                            template {
                                id
                                branches {
                                  id
                                }
                            }
                        }
                    }'
        ];
        $result = $this->graphQLQueriesExecutor->execute($mutations);
        $branchId = $result['createTemplate']['data']['createTemplate']['template']['branches'][0]['id'];
        $this->createCommitPost($branchId, $config);
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $technology
     * @param array $config
     * @param int $sortOrder
     * @param array $applyRulesConfig
     * @throws \Exception
     */
    private function createHookTemplatePost(
        $name,
        $technology,
        $config,
        $hookPriority,
        $hookName,
        $hookType,
        $hookAccArgs,
        $clearExistingHooks,
        $applyRulesConfig
    ) {
        $name = str_replace('Template: ', '', $name);
        $name = explode('(tag', $name)[0];
        $serializedApplyRulesConfig = json_encode($applyRulesConfig, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
        $serializedApplyRulesConfig = str_replace('\\', '\\\\', $serializedApplyRulesConfig);
        $serializedApplyRulesConfig = str_replace('"', '\\"', $serializedApplyRulesConfig);
        $mutations = [];
        $mutations[] = [
            'name' => 'createTemplate',
            'query' => 'mutation{
                        createTemplate(input: {
                            title: "' . $name . '",
                            type: "template",
                            sub_type: "hook",
                            technology: "' . $technology . '",
                            sort_order: ' . $hookPriority . ',
                            hook: "' . $hookName . '",
                            hook_type: ' . $hookType . ',
                            hook_accepted_args: ' . $hookAccArgs . ',
                            clear_existing_hooks: ' . ($clearExistingHooks ? 'true' : 'false') . ',
                            serialized_apply_rules_config: "' . $serializedApplyRulesConfig . '"
                        }){
                            template {
                                id
                                branches {
                                  id
                                }
                            }
                        }
                    }'
        ];
        $result = $this->graphQLQueriesExecutor->execute($mutations);
        $branchId = $result['createTemplate']['data']['createTemplate']['template']['branches'][0]['id'];
        $this->createCommitPost($branchId, $config);
    }

    /**
     * @param int $branchId
     * @param array $config
     * @throws \Exception
     */
    private function createCommitPost($branchId, $config)
    {
        $config = $this->configVersionConverter->convert($config);
        $serializedConfig = json_encode($config, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
        $serializedConfig = str_replace('\\', '\\\\', $serializedConfig);
        $serializedConfig = str_replace('"', '\\"', $serializedConfig);

        $mutations[] =
            [
                'name' => 'createCommit',
                'query' => 'mutation {
                                            createCommit(input: {
                                                branch_id: ' . $branchId . ', 
                                                serialized_content_config: "' . $serializedConfig . '", 
                                                description: "Revision"
                                                }) {
                                                    commit {
                                                        id
                                                    }
                                                }    
                                            }'
            ];
        $this->graphQLQueriesExecutor->execute($mutations);
    }
}