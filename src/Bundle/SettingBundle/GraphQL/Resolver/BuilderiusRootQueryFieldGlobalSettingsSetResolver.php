<?php

namespace Builderius\Bundle\SettingBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\SettingBundle\Factory\BuilderiusGlobalSettingsSetFromPostFactory;
use Builderius\Bundle\SettingBundle\Model\BuilderiusGlobalSettingsSet;
use Builderius\Bundle\SettingBundle\Registration\BuilderiusGlobalSettingsSetPostType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusRootQueryFieldGlobalSettingsSetResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusGlobalSettingsSetFromPostFactory
     */
    private $globalSettingsSetFromPostFactory;

    /**
     * @param \WP_Query $wpQuery
     * @param BuilderiusGlobalSettingsSetFromPostFactory $globalSettingsSetFromPostFactory
     */
    public function __construct(
        \WP_Query $wpQuery,
        BuilderiusGlobalSettingsSetFromPostFactory $globalSettingsSetFromPostFactory
    ) {
        $this->wpQuery = $wpQuery;
        $this->globalSettingsSetFromPostFactory = $globalSettingsSetFromPostFactory;
    }

    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['BuilderiusRootQuery'];
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'global_settings_set';
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
        $posts = $this->wpQuery->query([
            'p' => $args[BuilderiusGlobalSettingsSet::ID_FIELD],
            'post_type' => BuilderiusGlobalSettingsSetPostType::POST_TYPE,
            'post_status' => get_post_stati(),
            'posts_per_page' => 1,
            'no_found_rows' => true,
        ]);
        if (empty($posts)) {
            throw new \Exception('There is no Builderius GlobalSettings Set with provided ID', 400);
        }
        $post = reset($posts);

        return $this->globalSettingsSetFromPostFactory->createGlobalSettingsSet($post);
    }
}