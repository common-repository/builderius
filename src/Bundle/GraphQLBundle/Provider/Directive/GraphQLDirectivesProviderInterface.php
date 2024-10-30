<?php

namespace Builderius\Bundle\GraphQLBundle\Provider\Directive;

use Builderius\GraphQL\Type\Definition\Directive;

interface GraphQLDirectivesProviderInterface
{
    /**
     * @return Directive[]
     */
    public function getDirectives();

    /**
     * @param string $name
     * @return Directive
     */
    public function getDirective($name);

    /**
     * @param string $name
     * @return bool
     */
    public function hasDirective($name);
}