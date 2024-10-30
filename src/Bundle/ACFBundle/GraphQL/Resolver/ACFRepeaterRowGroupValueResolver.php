<?php

namespace Builderius\Bundle\ACFBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Helper\GraphQLLocalVarsAwareHelper;
use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class ACFRepeaterRowGroupValueResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var GraphQLLocalVarsAwareHelper
     */
    protected $localVarsHelper;

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
        return ['AcfRepeaterRow'];
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'acf_group_value';
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
        if (!isset($args['name'])) {
            return [];
        }
        if ($this->localVarsHelper) {
            $args = $this->localVarsHelper->processArguments($args, $info->path);
        }

        return (isset($objectValue[$args['name']]) && is_array($objectValue[$args['name']])) ? $objectValue[$args['name']] : [];
    }
}