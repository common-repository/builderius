<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

use ArrayObject;
use Exception;
use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Error\InvariantViolation;
use Builderius\GraphQL\Language\AST\EnumTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\EnumTypeExtensionNode;
use Builderius\GraphQL\Language\AST\EnumValueNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Utils\MixedStore;
use Builderius\GraphQL\Utils\Utils;
use function is_array;
use function is_int;
use function is_string;
use function sprintf;
class EnumType extends \Builderius\GraphQL\Type\Definition\Type implements \Builderius\GraphQL\Type\Definition\InputType, \Builderius\GraphQL\Type\Definition\OutputType, \Builderius\GraphQL\Type\Definition\LeafType, \Builderius\GraphQL\Type\Definition\NullableType, \Builderius\GraphQL\Type\Definition\NamedType
{
    /** @var EnumTypeDefinitionNode|null */
    public $astNode;
    /**
     * Lazily initialized.
     *
     * @var EnumValueDefinition[]
     */
    private $values;
    /**
     * Lazily initialized.
     *
     * Actually a MixedStore<mixed, EnumValueDefinition>, PHPStan won't let us type it that way.
     *
     * @var MixedStore
     */
    private $valueLookup;
    /** @var ArrayObject<string, EnumValueDefinition> */
    private $nameLookup;
    /** @var EnumTypeExtensionNode[] */
    public $extensionASTNodes;
    public function __construct($config)
    {
        if (!isset($config['name'])) {
            $config['name'] = $this->tryInferName();
        }
        \Builderius\GraphQL\Utils\Utils::invariant(\is_string($config['name']), 'Must provide name.');
        $this->name = $config['name'];
        $this->description = $config['description'] ?? null;
        $this->astNode = $config['astNode'] ?? null;
        $this->extensionASTNodes = $config['extensionASTNodes'] ?? null;
        $this->config = $config;
    }
    /**
     * @param string|mixed[] $name
     *
     * @return EnumValueDefinition|null
     */
    public function getValue($name)
    {
        $lookup = $this->getNameLookup();
        if (!\is_string($name)) {
            return null;
        }
        return $lookup[$name] ?? null;
    }
    private function getNameLookup() : \ArrayObject
    {
        if (!$this->nameLookup) {
            /** @var ArrayObject<string, EnumValueDefinition> $lookup */
            $lookup = new \ArrayObject();
            foreach ($this->getValues() as $value) {
                $lookup[$value->name] = $value;
            }
            $this->nameLookup = $lookup;
        }
        return $this->nameLookup;
    }
    /**
     * @return EnumValueDefinition[]
     */
    public function getValues() : array
    {
        if (!isset($this->values)) {
            $this->values = [];
            $config = $this->config;
            if (isset($config['values'])) {
                if (!\is_array($config['values'])) {
                    throw new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('%s values must be an array', $this->name));
                }
                foreach ($config['values'] as $name => $value) {
                    if (\is_string($name)) {
                        if (\is_array($value)) {
                            $value += ['name' => $name, 'value' => $name];
                        } else {
                            $value = ['name' => $name, 'value' => $value];
                        }
                    } elseif (\is_int($name) && \is_string($value)) {
                        $value = ['name' => $value, 'value' => $value];
                    } else {
                        throw new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('%s values must be an array with value names as keys.', $this->name));
                    }
                    $this->values[] = new \Builderius\GraphQL\Type\Definition\EnumValueDefinition($value);
                }
            }
        }
        return $this->values;
    }
    /**
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws Error
     */
    public function serialize($value)
    {
        $lookup = $this->getValueLookup();
        if (isset($lookup[$value])) {
            return $lookup[$value]->name;
        }
        throw new \Builderius\GraphQL\Error\Error('Cannot serialize value as enum: ' . \Builderius\GraphQL\Utils\Utils::printSafe($value));
    }
    /**
     * Actually returns a MixedStore<mixed, EnumValueDefinition>, PHPStan won't let us type it that way
     */
    private function getValueLookup() : \Builderius\GraphQL\Utils\MixedStore
    {
        if (!isset($this->valueLookup)) {
            $this->valueLookup = new \Builderius\GraphQL\Utils\MixedStore();
            foreach ($this->getValues() as $valueName => $value) {
                $this->valueLookup->offsetSet($value->value, $value);
            }
        }
        return $this->valueLookup;
    }
    /**
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws Error
     */
    public function parseValue($value)
    {
        $lookup = $this->getNameLookup();
        if (isset($lookup[$value])) {
            return $lookup[$value]->value;
        }
        throw new \Builderius\GraphQL\Error\Error('Cannot represent value as enum: ' . \Builderius\GraphQL\Utils\Utils::printSafe($value));
    }
    /**
     * @param mixed[]|null $variables
     *
     * @return null
     *
     * @throws Exception
     */
    public function parseLiteral(\Builderius\GraphQL\Language\AST\Node $valueNode, ?array $variables = null)
    {
        if ($valueNode instanceof \Builderius\GraphQL\Language\AST\EnumValueNode) {
            $lookup = $this->getNameLookup();
            if (isset($lookup[$valueNode->value])) {
                $enumValue = $lookup[$valueNode->value];
                if ($enumValue !== null) {
                    return $enumValue->value;
                }
            }
        }
        // Intentionally without message, as all information already in wrapped Exception
        throw new \Builderius\GraphQL\Error\Error();
    }
    /**
     * @throws InvariantViolation
     */
    public function assertValid()
    {
        parent::assertValid();
        \Builderius\GraphQL\Utils\Utils::invariant(isset($this->config['values']), \sprintf('%s values must be an array.', $this->name));
        $values = $this->getValues();
        foreach ($values as $value) {
            \Builderius\GraphQL\Utils\Utils::invariant(!isset($value->config['isDeprecated']), \sprintf('%s.%s should provide "deprecationReason" instead of "isDeprecated".', $this->name, $value->name));
        }
    }
}