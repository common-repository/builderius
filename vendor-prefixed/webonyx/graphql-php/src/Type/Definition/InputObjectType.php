<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Type\Definition;

use Builderius\GraphQL\Error\InvariantViolation;
use Builderius\GraphQL\Language\AST\InputObjectTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\InputObjectTypeExtensionNode;
use Builderius\GraphQL\Utils\Utils;
use function call_user_func;
use function count;
use function is_array;
use function is_callable;
use function is_string;
use function sprintf;
class InputObjectType extends \Builderius\GraphQL\Type\Definition\Type implements \Builderius\GraphQL\Type\Definition\InputType, \Builderius\GraphQL\Type\Definition\NullableType, \Builderius\GraphQL\Type\Definition\NamedType
{
    /** @var InputObjectTypeDefinitionNode|null */
    public $astNode;
    /**
     * Lazily initialized.
     *
     * @var InputObjectField[]
     */
    private $fields;
    /** @var InputObjectTypeExtensionNode[] */
    public $extensionASTNodes;
    /**
     * @param mixed[] $config
     */
    public function __construct(array $config)
    {
        if (!isset($config['name'])) {
            $config['name'] = $this->tryInferName();
        }
        \Builderius\GraphQL\Utils\Utils::invariant(\is_string($config['name']), 'Must provide name.');
        $this->config = $config;
        $this->name = $config['name'];
        $this->astNode = $config['astNode'] ?? null;
        $this->description = $config['description'] ?? null;
        $this->extensionASTNodes = $config['extensionASTNodes'] ?? null;
    }
    /**
     * @throws InvariantViolation
     */
    public function getField(string $name) : \Builderius\GraphQL\Type\Definition\InputObjectField
    {
        if (!isset($this->fields)) {
            $this->initializeFields();
        }
        \Builderius\GraphQL\Utils\Utils::invariant(isset($this->fields[$name]), "Field '%s' is not defined for type '%s'", $name, $this->name);
        return $this->fields[$name];
    }
    /**
     * @return InputObjectField[]
     */
    public function getFields() : array
    {
        if (!isset($this->fields)) {
            $this->initializeFields();
        }
        return $this->fields;
    }
    protected function initializeFields() : void
    {
        $this->fields = [];
        $fields = $this->config['fields'] ?? [];
        if (\is_callable($fields)) {
            $fields = $fields();
        }
        if (!\is_array($fields)) {
            throw new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('%s fields must be an array or a callable which returns such an array.', $this->name));
        }
        foreach ($fields as $name => $field) {
            if ($field instanceof \Builderius\GraphQL\Type\Definition\Type) {
                $field = ['type' => $field];
            }
            $field = new \Builderius\GraphQL\Type\Definition\InputObjectField($field + ['name' => $name]);
            $this->fields[$field->name] = $field;
        }
    }
    /**
     * Validates type config and throws if one of type options is invalid.
     * Note: this method is shallow, it won't validate object fields and their arguments.
     *
     * @throws InvariantViolation
     */
    public function assertValid() : void
    {
        parent::assertValid();
        \Builderius\GraphQL\Utils\Utils::invariant(\count($this->getFields()) > 0, \sprintf('%s fields must be an associative array with field names as keys or a callable which returns such an array.', $this->name));
        foreach ($this->getFields() as $field) {
            $field->assertValid($this);
        }
    }
}
