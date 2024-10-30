<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Validator\ValidationContext;
class CustomValidationRule extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    /** @var callable */
    private $visitorFn;
    public function __construct($name, callable $visitorFn)
    {
        $this->name = $name;
        $this->visitorFn = $visitorFn;
    }
    /**
     * @return Error[]
     */
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        $fn = $this->visitorFn;
        return $fn($context);
    }
}
