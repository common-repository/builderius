<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Validator\Rules;

use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Language\AST\FragmentSpreadNode;
use Builderius\GraphQL\Language\AST\NodeKind;
use Builderius\GraphQL\Validator\ValidationContext;
use function sprintf;
class KnownFragmentNames extends \Builderius\GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\Builderius\GraphQL\Validator\ValidationContext $context)
    {
        return [\Builderius\GraphQL\Language\AST\NodeKind::FRAGMENT_SPREAD => static function (\Builderius\GraphQL\Language\AST\FragmentSpreadNode $node) use($context) : void {
            $fragmentName = $node->name->value;
            $fragment = $context->getFragment($fragmentName);
            if ($fragment) {
                return;
            }
            $context->reportError(new \Builderius\GraphQL\Error\Error(self::unknownFragmentMessage($fragmentName), [$node->name]));
        }];
    }
    /**
     * @param string $fragName
     */
    public static function unknownFragmentMessage($fragName)
    {
        return \sprintf('Unknown fragment "%s".', $fragName);
    }
}
