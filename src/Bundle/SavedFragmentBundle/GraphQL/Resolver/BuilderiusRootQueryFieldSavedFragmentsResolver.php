<?php

namespace Builderius\Bundle\SavedFragmentBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
use Builderius\Bundle\SavedFragmentBundle\Factory\BuilderiusSavedFragmentFromPostFactory;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentPostType;
use Builderius\Bundle\SavedFragmentBundle\Registration\BuilderiusSavedFragmentTypeTaxonomy;

class BuilderiusRootQueryFieldSavedFragmentsResolver implements GraphQLFieldResolverInterface
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
        return 'saved_fragments';
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
            'post_type' => BuilderiusSavedFragmentPostType::POST_TYPE,
            'post_status' => get_post_stati(),
            'posts_per_page' => -1,
            'no_found_rows' => true,
        ];
        if (isset($args['type']) || isset($args['technology'])) {
            $queryArgs['tax_query'] = [];
            if (isset($args['type'])) {
                $queryArgs['tax_query'][] = [
                    'taxonomy' => BuilderiusSavedFragmentTypeTaxonomy::NAME,
                    'field' => 'slug',
                    'include_children' => false,
                    'terms' => [$args['type']]
                ];
            }
            if (isset($args['technology'])) {
                $queryArgs['tax_query'][] = [
                    'taxonomy' => BuilderiusTemplateTechnologyTaxonomy::NAME,
                    'field' => 'slug',
                    'include_children' => false,
                    'terms' => [$args['technology']]
                ];
            }
        }
        if (isset($args['author_id'])) {
            $queryArgs['author'] = $args['author_id'];
        }
        if (isset($args['author_name'])) {
            $queryArgs['author_name'] = $args['author_name'];
        }
        $posts = $this->wpQuery->query($queryArgs);
        $savedFragments = [];
        foreach ($posts as $post) {
            $savedFragments[] = BuilderiusSavedFragmentFromPostFactory::createSavedFragment($post);
        }

        return $savedFragments;
    }
}