<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\FieldNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Validator\ValidationContext;
class DisableIntrospection extends \Builderius\GraphQL\Validator\Rules\QuerySecurityRule
{
    public const ENABLED = 1;
    /** @var bool */
    private $isEnabled;
    public function __construct($enabled = self::ENABLED)
    {
        $this->setEnabled($enabled);
    }
    public function setEnabled($enabled)
    {
        $this->isEnabled = $enabled;
    }
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        return $this->invokeIfNeeded($context, [\Builderius\GraphQL\Language\AST\NodeKind::FIELD => static function (\Builderius\GraphQL\Language\AST\FieldNode $node) use($context) : void {
            if ($node->name->value !== '__type' && $node->name->value !== '__schema') {
                return;
            }
            $context->reportError(new \Builderius\GraphQL\Error\Error(static::introspectionDisabledMessage(), [$node]));
        }]);
    }
    public static function introspectionDisabledMessage()
    {
        return 'GraphQL introspection is not allowed, but the query contained __schema or __type';
    }
    protected function isEnabled()
    {
        return $this->isEnabled !== self::DISABLED;
    }
}
