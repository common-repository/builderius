<?php

namespace Builderius\Bundle\TemplateBundle\Provider\DataVar;

use Builderius\Bundle\GraphQLBundle\Event\GraphQLQueryBeforeExecutionEvent;
use Builderius\Bundle\GraphQLBundle\Event\GraphQLQueryExecutedEvent;
use Builderius\Bundle\GraphQLBundle\Provider\Directive\GraphQLDirectivesProviderInterface;
use Builderius\Bundle\GraphQLBundle\Provider\Type\GraphQLTypesProviderInterface;
use Builderius\Bundle\GraphQLBundle\Resolver\DefaultFieldResolver;
use Builderius\Bundle\GraphQLBundle\Type\RootTypeInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\GraphQL\Cache\GraphQLObjectCache;
use Builderius\GraphQL\GraphQL;
use Builderius\GraphQL\Type\Definition\Type;
use Builderius\GraphQL\Type\Schema;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class GraphQLQueryDataVarValueGenerator implements DataVarValueGeneratorInterface
{
    /**
     * @var GraphQLTypesProviderInterface
     */
    private $graphqlTypesProvider;

    /**
     * @var GraphQLDirectivesProviderInterface
     */
    private $graphqlDirectivesProvider;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $runtimeCache;

    /**
     * @var GraphQLObjectCache
     */
    private $graphqlSubfieldsCache;

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param GraphQLTypesProviderInterface $graphqlTypesProvider
     * @param GraphQLDirectivesProviderInterface $graphqlDirectivesProvider,
     * @param BuilderiusRuntimeObjectCache $runtimeCache
     * @param GraphQLObjectCache $graphqlSubfieldsCache
     * @param ExpressionLanguage $expressionLanguage
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        GraphQLTypesProviderInterface $graphqlTypesProvider,
        GraphQLDirectivesProviderInterface $graphqlDirectivesProvider,
        BuilderiusRuntimeObjectCache $runtimeCache,
        GraphQLObjectCache $graphqlSubfieldsCache,
        ExpressionLanguage $expressionLanguage,
        EventDispatcher $eventDispatcher
    ) {
        $this->graphqlTypesProvider = $graphqlTypesProvider;
        $this->graphqlDirectivesProvider = $graphqlDirectivesProvider;
        $this->runtimeCache = $runtimeCache;
        $this->graphqlSubfieldsCache = $graphqlSubfieldsCache;
        $this->expressionLanguage = $expressionLanguage;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 'graphQLQuery';
    }

    /**
     * @inheritDoc
     */
    public function getDependsOnDataVars(array $dataVarConfig)
    {
        if(isset($dataVarConfig['value']) && is_object($dataVarConfig['value'])) {
            $dataVarConfig['value'] = (array)$dataVarConfig['value'];
        }
        $dependsOnDataVars = $this->runtimeCache->get(sprintf('%s_depends_on_data_vars', $dataVarConfig['name']));
        if (false === $dependsOnDataVars) {
            $dependsOnDataVars = [];
            $query = $dataVarConfig['value']['query'];
            if (strpos($query, '"[[') !== false && strpos($query, ']]"') !== false) {
                preg_match_all('/"\[\[\[(.*?)\]\]\]"/s', $query, $nonEscapedDataVars);
                $nonEscapedDataVarsNames = array_unique($nonEscapedDataVars[1]);
                foreach ($nonEscapedDataVarsNames as $nonEscapedDataVarName) {
                    $varName = explode('.', explode('[', $nonEscapedDataVarName)[0])[0];
                    if (!in_array($varName, $dependsOnDataVars) && !empty($varName)) {
                        $dependsOnDataVars[] = $varName;
                    }
                }

                preg_match_all('/"\[\[(.*?)\]\]"/s', $query, $escapedDataVars);
                $escapedDataVarsNames = array_unique($escapedDataVars[1]);
                foreach ($escapedDataVarsNames as $escapedDataVarName) {
                    $varName = explode('.', explode('[', $escapedDataVarName)[0])[0];
                    if (!in_array($varName, $dependsOnDataVars) && !empty($varName)) {
                        $dependsOnDataVars[] = $varName;
                    }
                }
            }
            if ($dataVarConfig['value']['variables'] !== null) {
                $variables = is_string($dataVarConfig['value']['variables']) ?
                    json_decode($dataVarConfig['value']['variables'], true) : $dataVarConfig['value']['variables'];
                foreach ($variables as $variableValue) {
                    if (strpos($variableValue, '[[') !== false && strpos($variableValue, ']]') !== false) {
                        $dependsOnDataVarPath = str_replace('[[', '', str_replace(']]', '', str_replace('[[[', '', str_replace(']]]', '', $variableValue))));
                        $dependsOnDataVars[] = explode('.', explode('[', $dependsOnDataVarPath)[0])[0];
                    }
                }
            }
            $this->runtimeCache->set(sprintf('%s_depends_on_data_vars', $dataVarConfig['name']), $dependsOnDataVars);
        }

        return $dependsOnDataVars;
    }

    /**
     * @inheritDoc
     */
    public function generateValue($templateType, $dataVarName, array $dataVarsConfigs, array $dataVarsValues)
    {
        $dataVarConfig = $dataVarsConfigs[$dataVarName];
        $rootType = $this->runtimeCache->get(sprintf("%s_graphql_root_type", $templateType));
        if (false === $rootType) {
            $rootTypes = array_filter(
                $this->graphqlTypesProvider->getTypes(),
                function (Type $type) use ($templateType) {
                    if ($type instanceof RootTypeInterface &&
                        ($templateType === $type->getTemplateType() || ($templateType === 'all' && $type->isAppliedToAllTemplateTypes())
                        )
                    ) {
                        return true;
                    }
                    return false;
                }
            );
            if (empty($rootTypes)) {
                $dataVarsValues[$dataVarName] = null;
            }
            /** @var RootTypeInterface|Type $rootType */
            $rootType = reset($rootTypes);
            $this->runtimeCache->set(sprintf("%s_graphql_root_type", $templateType), $rootType);
        }
        $schema = new Schema([
            'query' => $rootType,
            'directives' => $this->graphqlDirectivesProvider->getDirectives()
        ]);
        $rootData = null;
        if ($rootDataProvider = $rootType->getRootDataProvider() ) {
            $rootData = $rootDataProvider->getRootData();
        }
        if(isset($dataVarConfig['value']) && is_object($dataVarConfig['value'])) {
            $dataVarConfig['value'] = (array)$dataVarConfig['value'];
        }
        $query = $dataVarConfig['value']['query'];
        if (strpos($query, '"[[') !== false && strpos($query, ']]"') !== false) {
            preg_match_all('/"\[\[\[(.*?)\]\]\]"/s', $query, $nonEscapedDataVars);
            $nonEscapedDataVarsPaths = array_unique($nonEscapedDataVars[1]);
            foreach ($nonEscapedDataVarsPaths as $nonEscapedDataVarPath) {
                try {
                    $dataVarValue = $this->expressionLanguage->evaluate($nonEscapedDataVarPath, $dataVarsValues);
                    if (!is_string($dataVarValue)) {
                        $query = str_replace(sprintf('"[[[%s]]]"',$nonEscapedDataVarPath), $dataVarValue, $query);
                    } else {
                        $query = str_replace(sprintf('[[[%s]]]',$nonEscapedDataVarPath), $dataVarValue, $query);
                    }

                } catch (\Exception|\Error $e) {
                    $query = str_replace(sprintf('"[[[%s]]]"',$nonEscapedDataVarPath), '""', $query);
                }
            }

            preg_match_all('/"\[\[(.*?)\]\]"/s', $query, $escapedDataVars);
            $escapedDataVarsPaths = array_unique($escapedDataVars[1]);
            foreach ($escapedDataVarsPaths as $escapedDataVarPath) {
                try {
                    $dataVarValue = $this->expressionLanguage->evaluate($escapedDataVarPath, $dataVarsValues);
                    if (!is_string($dataVarValue)) {
                        $query = str_replace(sprintf('"[[%s]]"',$escapedDataVarPath), $dataVarValue, $query);
                    } else {
                        $query = str_replace(sprintf('[[%s]]',$escapedDataVarPath), $dataVarValue, $query);
                    }
                } catch (\Exception|\Error $e) {
                    $query = str_replace(sprintf('"[[%s]]"',$escapedDataVarPath), '""', $query);
                }
            }
        }
        if ($dataVarConfig['value']['variables'] === null) {
            $variables = null;
        } else {
            $variables = is_string($dataVarConfig['value']['variables']) ?
                json_decode($dataVarConfig['value']['variables'], true) : $dataVarConfig['value']['variables'];
            foreach ($variables as $variableName => $variableValue) {
                if (strpos($variableValue, '[[') !== false && strpos($variableValue, ']]') !== false) {
                    $valuePath = str_replace('[[', '', str_replace(']]', '', str_replace('[[[', '', str_replace(']]]', '', $variableValue))));
                    try {
                        $variables[$variableName] = $this->expressionLanguage->evaluate($valuePath, $dataVarsValues);
                    } catch (\Exception|\Error $e) {
                        $variables[$variableName] = '';
                    }
                }
            }
        }
        try {
            $event = new GraphQLQueryBeforeExecutionEvent($query);
            $this->eventDispatcher->dispatch($event, 'builderius_graphql_query_before_execution');
            $query = $event->getQuery();
            $graphqlResponse = GraphQL::executeQuery(
                $schema,
                $query,
                $rootData,
                null,
                $variables,
                null,
                [DefaultFieldResolver::class, 'resolve'],
                null,
                $this->graphqlSubfieldsCache,
                $this->eventDispatcher
            );
            $event = new GraphQLQueryExecutedEvent($query, $graphqlResponse);
            $this->eventDispatcher->dispatch($event, 'builderius_graphql_query_executed');
            $graphqlResponse = $event->getResult();
            $dataVarsValues[$dataVarName] = $graphqlResponse->data;
        } catch (\Exception|\Error $e) {
            $dataVarsValues[$dataVarName] = null;
        }

        return $dataVarsValues;
    }
}