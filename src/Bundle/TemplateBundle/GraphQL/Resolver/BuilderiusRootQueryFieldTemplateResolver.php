<?php

namespace Builderius\Bundle\TemplateBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusRootQueryFieldTemplateResolver implements GraphQLFieldResolverInterface
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
     * @param \WP_Query $wpQuery
     * @param BuilderiusTemplateFromPostFactory $templateFromPostFactory
     */
    public function __construct(\WP_Query $wpQuery, BuilderiusTemplateFromPostFactory $templateFromPostFactory)
    {
        $this->wpQuery = $wpQuery;
        $this->templateFromPostFactory = $templateFromPostFactory;
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
        return 'template';
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
            'p' => $args[BuilderiusTemplate::ID_FIELD],
            'post_type' => BuilderiusTemplatePostType::POST_TYPE,
            'post_status' => get_post_stati(),
            'posts_per_page' => 1,
            'no_found_rows' => true,
        ]);
        if (empty($posts)) {
            throw new \Exception('There is no Builderius Template with provided ID', 400);
        }
        $post = reset($posts);

        return $this->templateFromPostFactory->createTemplate($post);
    }
}