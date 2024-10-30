<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

use Builderius\GraphQL\Error\InvariantViolation;
use Builderius\GraphQL\Language\AST\InputValueDefinitionNode;
use Builderius\GraphQL\Type\Schema;
use Builderius\GraphQL\Utils\Utils;
use function array_key_exists;
use function is_array;
use function is_string;
use function sprintf;
class FieldArgument
{
    /** @var string */
    public $name;
    /** @var mixed */
    public $defaultValue;
    /** @var string|null */
    public $description;
    /** @var InputValueDefinitionNode|null */
    public $astNode;
    /** @var mixed[] */
    public $config;
    /** @var Type&InputType */
    private $type;
    /** @param mixed[] $def */
    public function __construct(array $def)
    {
        foreach ($def as $key => $value) {
            switch ($key) {
                case 'name':
                    $this->name = $value;
                    break;
                case 'defaultValue':
                    $this->defaultValue = $value;
                    break;
                case 'description':
                    $this->description = $value;
                    break;
                case 'astNode':
                    $this->astNode = $value;
                    break;
            }
        }
        $this->config = $def;
    }
    /**
     * @param mixed[] $config
     *
     * @return FieldArgument[]
     */
    public static function createMap(array $config) : array
    {
        $map = [];
        foreach ($config as $name => $argConfig) {
            if (!\is_array($argConfig)) {
                $argConfig = ['type' => $argConfig];
            }
            $map[] = new self($argConfig + ['name' => $name]);
        }
        return $map;
    }
    public function getType() : \Builderius\GraphQL\Type\Definition\Type
    {
        if (!isset($this->type)) {
            /**
             * TODO: replace this phpstan cast with native assert
             *
             * @var Type&InputType
             */
            $type = \Builderius\GraphQL\Type\Schema::resolveType($this->config['type']);
            $this->type = $type;
        }
        return $this->type;
    }
    public function defaultValueExists() : bool
    {
        return \array_key_exists('defaultValue', $this->config);
    }
    public function isRequired() : bool
    {
        return $this->getType() instanceof \Builderius\GraphQL\Type\Definition\NonNull && !$this->defaultValueExists();
    }
    public function assertValid(\Builderius\GraphQL\Type\Definition\FieldDefinition $parentField, \Builderius\GraphQL\Type\Definition\Type $parentType)
    {
        try {
            \Builderius\GraphQL\Utils\Utils::assertValidName($this->name);
        } catch (\Builderius\GraphQL\Error\InvariantViolation $e) {
            throw new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('%s.%s(%s:) %s', $parentType->name, $parentField->name, $this->name, $e->getMessage()));
        }
        $type = $this->getType();
        if ($type instanceof \Builderius\GraphQL\Type\Definition\WrappingType) {
            $type = $type->getWrappedType(\true);
        }
        \Builderius\GraphQL\Utils\Utils::invariant($type instanceof \Builderius\GraphQL\Type\Definition\InputType, \sprintf('%s.%s(%s): argument type must be Input Type but got: %s', $parentType->name, $parentField->name, $this->name, \Builderius\GraphQL\Utils\Utils::printSafe($this->type)));
        \Builderius\GraphQL\Utils\Utils::invariant($this->description === null || \is_string($this->description), \sprintf('%s.%s(%s): argument description type must be string but got: %s', $parentType->name, $parentField->name, $this->name, \Builderius\GraphQL\Utils\Utils::printSafe($this->description)));
    }
}
