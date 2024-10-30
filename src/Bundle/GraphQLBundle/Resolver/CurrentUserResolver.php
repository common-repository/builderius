<?php


namespace Builderius\Bundle\GraphQLBundle\Resolver;

use Builderius\GraphQL\Type\Definition\ResolveInfo;

class CurrentUserResolver implements GraphQLFieldResolverInterface
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
        return 'current_user';
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
        $user = wp_get_current_user();
        if ($user instanceof \WP_User) {
            return $user;
        }

        return new \WP_User();
    }
}