<?php

namespace Builderius\Bundle\ACFBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Helper\GraphQLLocalVarsAwareHelper;
use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class ACFRepeaterRowPostObjectValueResolver implements GraphQLFieldResolverInterface
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
        return 'acf_post_object_value';
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
        if ($this->localVarsHelper) {
            $args = $this->localVarsHelper->processArguments($args, $info->path);
        }
        if (!isset($args['name']) || !isset($objectValue[$args['name']])) {
            return new \stdClass();
        }
        $val = $objectValue[$args['name']];
        if (is_int($val)) {
            $val = get_post($val);
        } elseif(is_array($val) && isset($val['ID'])) {
            $val = get_post($val['ID']);
        }

        return $val instanceof \WP_Post ? $val : new \stdClass();
    }
}