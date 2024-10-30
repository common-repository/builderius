<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Validator\SDLValidationContext;
use Builderius\GraphQL\Validator\ValidationContext;
use function class_alias;
abstract class ValidationRule
{
    /** @var string */
    protected $name;
    public function getName()
    {
        return $this->name === '' || $this->name === null ? static::class : $this->name;
    }
    public function __invoke(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        return $this->getVisitor($context);
    }
    /**
     * Returns structure suitable for GraphQL\Language\Visitor
     *
     * @see \GraphQL\Language\Visitor
     *
     * @return mixed[]
     */
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        return [];
    }
    /**
     * Returns structure suitable for GraphQL\Language\Visitor
     *
     * @see \GraphQL\Language\Visitor
     *
     * @return mixed[]
     */
    public function getSDLVisitor(\Builderius\GraphQL\Validator\SDLValidationContext $context)
    {
        return [];
    }
}
\class_alias(\Builderius\GraphQL\Validator\Rules\ValidationRule::class, 'Builderius\\GraphQL\\Validator\\Rules\\AbstractValidationRule');
