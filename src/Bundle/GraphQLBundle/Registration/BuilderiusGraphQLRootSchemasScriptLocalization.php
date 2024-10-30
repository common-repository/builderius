<?php

namespace Builderius\Bundle\GraphQLBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\GraphQLBundle\Provider\Directive\GraphQLDirectivesProviderInterface;
use Builderius\Bundle\GraphQLBundle\Provider\Type\GraphQLTypesProviderInterface;
use Builderius\Bundle\GraphQLBundle\Type\RootTypeInterface;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;
use Builderius\GraphQL\GraphQL;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Introspection;
use Builderius\GraphQL\Type\Schema;

class BuilderiusGraphQLRootSchemasScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'graphQLSchemas';

    /**
     * @var BuilderiusTemplateProviderInterface
     */
    private $templateProvider;

    /**
     * @var GraphQLTypesProviderInterface
     */
    private $graphqlTypesProvider;

    /**
     * @var GraphQLDirectivesProviderInterface
     */
    private $graphqlDirectivesProvider;

    /**
     * @param BuilderiusTemplateProviderInterface $templateProvider
     * @param GraphQLTypesProviderInterface $graphqlTypesProvider
     * @param GraphQLDirectivesProviderInterface $graphqlDirectivesProvider
     */
    public function __construct(
        BuilderiusTemplateProviderInterface $templateProvider,
        GraphQLTypesProviderInterface $graphqlTypesProvider,
        GraphQLDirectivesProviderInterface $graphqlDirectivesProvider
    ) {
        $this->templateProvider = $templateProvider;
        $this->graphqlTypesProvider = $graphqlTypesProvider;
        $this->graphqlDirectivesProvider = $graphqlDirectivesProvider;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $data = [];
        $template = $this->templateProvider->getTemplate();
        if ($template) {
            $templateType = $template->getType();
            $rootTypes = array_filter(
                $this->graphqlTypesProvider->getTypes(),
                function(Type $type) use ($templateType) {
                    if ($type instanceof RootTypeInterface &&
                        ($templateType === $type->getTemplateType() || $type->isAppliedToAllTemplateTypes())
                    ) {
                        return true;
                    }
                    return false;
                }
            );
            /** @var RootTypeInterface[] $rootTypes */
            foreach ($rootTypes as $rootType) {
                $rootType->name = 'RootQuery';
                $schema = new Schema([
                    'query' => $rootType,
                    'directives' => $this->graphqlDirectivesProvider->getDirectives()
                ]);
                $result = GraphQL::executeQuery(
                    $schema,
                    Introspection::getIntrospectionQuery()
                );
                $result = $result->data;
                if (isset($result['__schema']) && isset($result['__schema']['types'])) {
                    foreach ($result['__schema']['types'] as &$type) {
                        if (is_array($type['fields'])) {
                            usort($type['fields'], function (array $a, array $b) {
                                $aName = $a['name'];
                                $bName = $b['name'];
                                if ($aName < $bName) {
                                    return -1;
                                } elseif ($aName > $bName) {
                                    return 1;
                                } else {
                                    return 0;
                                }
                            });
                        } elseif (is_array($type['inputFields'])) {
                            usort($type['inputFields'], function (array $a, array $b) {
                                $aName = $a['name'];
                                $bName = $b['name'];
                                if ($aName < $bName) {
                                    return -1;
                                } elseif ($aName > $bName) {
                                    return 1;
                                } else {
                                    return 0;
                                }
                            });
                        }
                    }
                    unset($type);
                    if ($rootType->isAppliedToAllTemplateTypes()) {
                        $data['all'] = $result;
                    } elseif ($rootType->getTemplateType()) {
                        $data[$rootType->getTemplateType()] = $result;
                    }
                }
            }
        }

        return $data;
    }
}
