<?php

namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

class CurrentUrlResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var array
     */
    protected $typeNames;

    /**
     * @param array $typeNames
     */
    public function __construct(array $typeNames)
    {
        $this->typeNames = $typeNames;
    }

    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return $this->typeNames;
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'current_url';
    }

    /**
     * @inheritDoc
     */
    public function isApplicable($objectValue, array $args, $context, ResolveInfo $info)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
}