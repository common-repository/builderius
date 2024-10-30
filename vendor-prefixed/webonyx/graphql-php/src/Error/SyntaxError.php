<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Error;

use Builderius\GraphQL\Language\Source;
use function sprintf;
class SyntaxError extends \Builderius\GraphQL\Error\Error
{
    /**
     * @param int    $position
     * @param string $description
     */
    public function __construct(\Builderius\GraphQL\Language\Source $source, $position, $description)
    {
        parent::__construct(\sprintf('Syntax Error: %s', $description), null, $source, [$position]);
    }
}
