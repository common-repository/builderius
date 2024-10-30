<?php

namespace Builderius\Bundle\ModuleBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\ModuleBundle\Factory\BuilderiusSavedCompositeModuleFromPostFactory;
use Builderius\Bundle\ModuleBundle\Registration\BuilderiusSavedCompositeModulePostType;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusRootQueryFieldSavedCompositeModulesResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @param \WP_Query $wpQuery
     */
    public function __construct(\WP_Query $wpQuery)
    {
        $this->wpQuery = $wpQuery;
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
        return 'saved_composite_modules';
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
        $queryArgs = [
            'post_type' => BuilderiusSavedCompositeModulePostType::POST_TYPE,
            'post_status' => get_post_stati(),
            'posts_per_page' => -1,
            'no_found_rows' => true,
        ];
        if (isset($args['technology'])) {
            $queryArgs['tax_query'][] = [
                'taxonomy' => BuilderiusTemplateTechnologyTaxonomy::NAME,
                'field' => 'slug',
                'include_children' => false,
                'terms' => [$args['technology']]
            ];
        }
        $posts = $this->wpQuery->query($queryArgs);
        $savedCompositeModules = [];
        foreach ($posts as $post) {
            $savedCompositeModules[] = BuilderiusSavedCompositeModuleFromPostFactory::createSavedCompositeModule($post);
        }

        return $savedCompositeModules;
    }
}