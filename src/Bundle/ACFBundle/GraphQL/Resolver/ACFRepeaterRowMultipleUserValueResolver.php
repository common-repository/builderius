<?php

namespace Builderius\Bundle\ACFBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Helper\GraphQLLocalVarsAwareHelper;
use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class ACFRepeaterRowMultipleUserValueResolver implements GraphQLFieldResolverInterface
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
        return 'acf_multiple_user_value';
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
            return [];
        }
        $val = $objectValue[$args['name']];
        if (!is_array($val)) {
            return [];
        }
        foreach ($val as &$item) {
            if(is_int($item)) {
                $item = get_user_by('ID', $item);
            } elseif(is_array($item) && isset($item['ID'])) {
                $item = get_user_by('ID', $item['ID']);
            }
            if (!$item instanceof \WP_User) {
                return [];
            }
        }

        return $val;
    }
}