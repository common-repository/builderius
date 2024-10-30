<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

use Exception;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Utils\AST;
use Builderius\GraphQL\Utils\Utils;
use function is_callable;
use function sprintf;
class CustomScalarType extends \Builderius\GraphQL\Type\Definition\ScalarType
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function serialize($value)
    {
        return $this->config['serialize']($value);
    }
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function parseValue($value)
    {
        if (isset($this->config['parseValue'])) {
            return $this->config['parseValue']($value);
        }
        return $value;
    }
    /**
     * @param mixed[]|null $variables
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function parseLiteral(\Builderius\GraphQL\Language\AST\Node $valueNode, ?array $variables = null)
    {
        if (isset($this->config['parseLiteral'])) {
            return $this->config['parseLiteral']($valueNode, $variables);
        }
        return \Builderius\GraphQL\Utils\AST::valueFromASTUntyped($valueNode, $variables);
    }
    public function assertValid()
    {
        parent::assertValid();
        \Builderius\GraphQL\Utils\Utils::invariant(isset($this->config['serialize']) && \is_callable($this->config['serialize']), \sprintf('%s must provide "serialize" function. If this custom Scalar ', $this->name) . 'is also used as an input type, ensure "parseValue" and "parseLiteral" ' . 'functions are also provided.');
        if (!isset($this->config['parseValue']) && !isset($this->config['parseLiteral'])) {
            return;
        }
        \Builderius\GraphQL\Utils\Utils::invariant(isset($this->config['parseValue']) && isset($this->config['parseLiteral']) && \is_callable($this->config['parseValue']) && \is_callable($this->config['parseLiteral']), \sprintf('%s must provide both "parseValue" and "parseLiteral" functions.', $this->name));
    }
}
