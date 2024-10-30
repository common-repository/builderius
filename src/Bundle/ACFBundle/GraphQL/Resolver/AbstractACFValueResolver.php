<?php

namespace Builderius\Bundle\ACFBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Helper\GraphQLLocalVarsAwareHelper;
use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

abstract class AbstractACFValueResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var array
     */
    protected $typeNames;

    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var bool
     */
    protected $isOption = true;

    /**
     * @var GraphQLLocalVarsAwareHelper
     */
    protected $localVarsHelper;

    /**
     * @param array $typeNames
     * @param string $fieldName
     * @param bool $isOption
     */
    public function __construct(array $typeNames, string $fieldName, $isOption = true)
    {
        $this->typeNames = $typeNames;
        $this->fieldName = $fieldName;
        $this->isOption = $isOption;
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
    public function getSortOrder()
    {
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @inheritDoc
     */
    public function isApplicable($objectValue, array $args, $context, ResolveInfo $info)
    {
        return true;
    }

    /**
     * @param mixed $objectValue
     * @return string
     */
    protected function getOwnerId($objectValue)
    {
        if ($this->isOption) {
            return 'option';
        } elseif ($objectValue instanceof \WP_User) {
            return sprintf('user_%d', $objectValue->ID);
        } elseif ($objectValue instanceof \WP_Term) {
            return sprintf('term_%d', $objectValue->term_id);
        } elseif ($objectValue instanceof \WP_Comment) {
            return sprintf('comment_%d', $objectValue->comment_ID);
        } elseif ($objectValue instanceof \WP_Post) {
            return sprintf('post_%d', $objectValue->ID);
        }

        return $objectValue->ID;
    }
}