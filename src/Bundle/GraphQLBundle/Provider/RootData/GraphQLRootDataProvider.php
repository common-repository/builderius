<?php

namespace Builderius\Bundle\GraphQLBundle\Provider\RootData;

class GraphQLRootDataProvider implements BuilderiusGraphQLRootDataProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getRootData(array $args = [])
    {
        global $wp_query;
        $wp_query->reset_postdata();

        $queriedPost = new \WP_Post((object)[]);

        $queriedObject = get_queried_object();

        if ($queriedObject instanceof \WP_Post) {
            $queriedPost = $queriedObject;
        }

        return (object)[
            'queried_post' => $queriedPost
        ];
    }
}