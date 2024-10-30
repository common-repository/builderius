<?php

namespace Builderius\Bundle\TemplateBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTypeTaxonomy;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusRootQueryFieldTemplatesResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusTemplateFromPostFactory
     */
    private $templateFromPostFactory;

    /**
     * @var BuilderiusTemplateTypesProviderInterface
     */
    private $templateTypesProvider;

    /**
     * @param \WP_Query $wpQuery
     * @param BuilderiusTemplateFromPostFactory $templateFromPostFactory
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     */
    public function __construct(
        \WP_Query $wpQuery,
        BuilderiusTemplateFromPostFactory $templateFromPostFactory,
        BuilderiusTemplateTypesProviderInterface $templateTypesProvider
    ) {
        $this->wpQuery = $wpQuery;
        $this->templateFromPostFactory = $templateFromPostFactory;
        $this->templateTypesProvider = $templateTypesProvider;
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
        return 'templates';
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
            'post_type' => BuilderiusTemplatePostType::POST_TYPE,
            'post_status' => get_post_stati(),
            'posts_per_page' => -1,
            'no_found_rows' => true,
        ];
        if (isset($args['type']) || isset($args['technology'])) {
            $queryArgs['tax_query'] = [];
            if (isset($args['type'])) {
                $queryArgs['tax_query'][] = [
                    'taxonomy' => BuilderiusTemplateTypeTaxonomy::NAME,
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
        $templates = [];
        foreach ($posts as $post) {
            $template = $this->templateFromPostFactory->createTemplate($post);
            if (!isset($args['standalone'])) {
                $templates[] = $template;
            } else {
                $templateType = $this->templateTypesProvider->getType($template->getType());
                if ($args['standalone'] === true && $templateType->isStandalone()) {
                    $templates[] = $template;
                } elseif ($args['standalone'] === false && !$templateType->isStandalone()) {
                    $templates[] = $template;
                }
            }
        }

        return $templates;
    }
}