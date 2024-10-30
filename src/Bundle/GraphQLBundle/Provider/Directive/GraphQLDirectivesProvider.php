<?php

namespace Builderius\Bundle\GraphQLBundle\Provider\Directive;

use Builderius\Bundle\GraphQLBundle\Provider\Type\GraphQLTypesProviderInterface;
use Builderius\GraphQL\Type\Definition\Directive;
use Builderius\GraphQL\Type\Definition\FieldArgument;

class GraphQLDirectivesProvider implements GraphQLDirectivesProviderInterface
{
    /**
     * @var Directive[]
     */
    private $directives = [];

    /**
     * @var GraphQLTypesProviderInterface
     */
    private $graphqlTypesProvider;

    /**
     * @param GraphQLTypesProviderInterface $graphqlTypesProvider
     */
    public function __construct(GraphQLTypesProviderInterface $graphqlTypesProvider)
    {
        $this->graphqlTypesProvider = $graphqlTypesProvider;
    }

    /**
     * @param Directive $directive
     * @return $this
     */
    public function addDirective(Directive $directive)
    {
        $this->directives[$directive->name] = $directive;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDirectives()
    {
        $directives = [];
        foreach ($this->directives as $directive) {
            $directives[] = $this->preprocessDirective($directive);
        }

        return $directives;
    }

    /**
     * @inheritDoc
     */
    public function getDirective($name)
    {
        return $this->hasDirective($name) ? $this->getDirectives()[$name] : null;
    }

    /**
     * @inheritDoc
     */
    public function hasDirective($name)
    {
        return isset($this->getDirectives()[$name]);
    }

    /**
     * @param Directive $directive
     * @return Directive
     */
    private function preprocessDirective(Directive $directive)
    {   if (!empty($directive->args)) {
            $args = [];
            /** @var FieldArgument $arg */
        foreach($directive->args as $arg) {
                    $config = $arg->config;
                if (isset($config['type']) && is_string($config['type'])) {
                    $realType = $this->graphqlTypesProvider->getType($config['type']);
                    if ($realType) {
                        $config['type'] = $realType;
                        $arg->config = $config;
                    }
                }
                $args[] = $arg;
            }
            $directive->args = $args;
        }
        return $directive;
    }
}