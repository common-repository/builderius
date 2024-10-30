<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\DocumentNode;
use Builderius\GraphQL\Type\Schema;
abstract class ASTValidationContext
{
    /** @var DocumentNode */
    protected $ast;
    /** @var Error[] */
    protected $errors;
    /** @var Schema */
    protected $schema;
    public function __construct(\Builderius\GraphQL\Language\AST\DocumentNode $ast, ?\Builderius\GraphQL\Type\Schema $schema = null)
    {
        $this->ast = $ast;
        $this->schema = $schema;
        $this->errors = [];
    }
    public function reportError(\Builderius\GraphQL\Error\Error $error)
    {
        $this->errors[] = $error;
    }
    /**
     * @return Error[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
    /**
     * @return DocumentNode
     */
    public function getDocument()
    {
        return $this->ast;
    }
    public function getSchema() : ?\Builderius\GraphQL\Type\Schema
    {
        return $this->schema;
    }
}
