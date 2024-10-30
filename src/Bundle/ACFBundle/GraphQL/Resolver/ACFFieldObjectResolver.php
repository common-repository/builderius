<?php

namespace Builderius\Bundle\ACFBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Helper\GraphQLLocalVarsAwareHelper;
use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class ACFFieldObjectResolver implements GraphQLFieldResolverInterface
{
    private $typeNames;

    /**
     * @var GraphQLLocalVarsAwareHelper
     */
    private $localVarsHelper;

    /**
     * @param array $typeNames
     */
    public function __construct(
        array $typeNames
    )
    {
        $this->typeNames = $typeNames;
    }

    /**
     * @param GraphQLLocalVarsAwareHelper $localVarsHelper
     * @return $this
     */
    public function setLocalVarsHelper(GraphQLLocalVarsAwareHelper $localVarsHelper)
    {
        $this->localVarsHelper = $localVarsHelper;

        return $this;
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
    public function getFieldName()
    {
        return 'acf_field_object';
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
    public function isApplicable($objectValue, array $args, $context, ResolveInfo $info)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function resolve($objectValue, array $args, $context, ResolveInfo $info)
    {
        try {
            if ($this->localVarsHelper) {
                $args = $this->localVarsHelper->processArguments($args, $info->path);
            }
            return acf_get_field($args['name']);
        } catch (\Exception $e) {
            return null;
        }
    }
}