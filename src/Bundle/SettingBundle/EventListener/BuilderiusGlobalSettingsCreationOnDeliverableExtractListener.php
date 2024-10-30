<?php

namespace Builderius\Bundle\SettingBundle\EventListener;

use Builderius\Bundle\DeliverableBundle\Event\DeliverableContainingEvent;
use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\SettingBundle\Registration\BuilderiusGlobalSettingsSetPostType;
use Builderius\Bundle\TemplateBundle\Converter\Version\BuilderiusTemplateConfigVersionConverterInterface;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;

class BuilderiusGlobalSettingsCreationOnDeliverableExtractListener
{
    /**
     * @var BuilderiusEntitiesGraphQLQueriesExecutorInterface
     */
    private $graphQLQueriesExecutor;

    /**
     * @var BuilderiusTemplateConfigVersionConverterInterface
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
        foreach ($deliverable->getSubModules('global_settings_set') as $dsm) {
            $this->createGlobalSettingsSetPost($dsm->getTechnology(), $dsm->getContentConfig());
        }
    }

    /**
     * @param string $technology
     * @param array $config
     */
    private function createGlobalSettingsSetPost($technology, array $config)
    {
        $currUserId = apply_filters('builderius_get_current_user', wp_get_current_user())->ID;
        $time = current_time('mysql');
        $globalSettingsSetArguments = [
            'post_name' => $technology,
            'post_type' => BuilderiusGlobalSettingsSetPostType::POST_TYPE,
            'post_author' => $currUserId,
            'post_date' => $time,
            'post_date_gmt' => get_gmt_from_date($time),
        ];
        $globalSettingsSetPostId = wp_insert_post(wp_slash($globalSettingsSetArguments), true);
        if (!is_wp_error($globalSettingsSetPostId)) {
            $branchArguments = [
                'post_name' => 'master',
                'post_parent' => $globalSettingsSetPostId,
                'post_type' => BuilderiusBranchPostType::POST_TYPE,
                'post_author' => $currUserId,
                'post_date' => $time,
                'post_date_gmt' => get_gmt_from_date($time),
            ];
            $branchId = wp_insert_post(wp_slash($branchArguments), true);
            if (!is_wp_error($branchId)) {
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
    }
}