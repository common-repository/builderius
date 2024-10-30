<?php

namespace Builderius\Bundle\SavedFragmentBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;

class BuilderiusSavedFragmentsScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'savedFragments';

    /**
     * @var BuilderiusTemplateProviderInterface
     */
    private $builderiusTemplateProvider;

    /**
     * @var BuilderiusEntitiesGraphQLQueriesExecutorInterface
     */
    private $graphQLQueriesExecutor;

    /**
     * @param BuilderiusTemplateProviderInterface $builderiusTemplateProvider
     * @param BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor
     */
    public function __construct(
        BuilderiusTemplateProviderInterface $builderiusTemplateProvider,
        BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor
    ) {
        $this->builderiusTemplateProvider = $builderiusTemplateProvider;
        $this->graphQLQueriesExecutor = $graphQLQueriesExecutor;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $template = $this->builderiusTemplateProvider->getTemplate();
        if ($template) {
            $queries = [
                [
                    'name' => 'saved_fragments',
                    'query' => 'query {
                                layouts: saved_fragments(type: layout, technology:' . $template->getTechnology() . ') {
                                    id
                                    name
                                    title
                                    description
                                    static_content_config
                                    dynamic_content_config
                                    category
                                    tags
                                    created_at
                                    updated_at
                                    image
                                    author {
                                        nickname
                                    }
                                }
                            }'
                ]
            ];
            $results = $this->graphQLQueriesExecutor->execute($queries);

            $data = $results['saved_fragments']['data'];
            foreach ($data as $type => $groupedItems) {
                foreach ($groupedItems as $i => $item) {
                    if($data[$type][$i]['image'] == null) {
                        $data[$type][$i]['image'] = WP_PLUGIN_URL . '/builderius/assets/img/no-image-available.jpeg';
                    }
                }
            }

            return $data;
        }

        return [];
    }
}
