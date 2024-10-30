<?php

namespace Builderius\Bundle\VCSBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\ReleaseBundle\Registration\BulderiusReleasePostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusVCSTagTaxonomy;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusRootQueryFieldDeliverableVcsTagsResolver implements GraphQLFieldResolverInterface
{
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
    public function getSortOrder()
    {
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'deliverable_vcs_tags';
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
        global $wpdb;
        $sqlDeliverableTags = sprintf(
            "SELECT DISTINCT t.name from %s t inner join %s tt on t.term_id = tt.term_taxonomy_id left join %s tr on tr.term_taxonomy_id = t.term_id LEFT JOIN %s p on tr.object_id = p.ID WHERE tt.taxonomy = '%s' AND p.post_type = '%s' AND t.name NOT IN(SELECT DISTINCT p1.post_title from %s p1 where p1.post_type = '%s') GROUP BY t.name",
            $wpdb->terms,
            $wpdb->term_taxonomy,
            $wpdb->term_relationships,
            $wpdb->posts,
            BuilderiusVCSTagTaxonomy::NAME,
            BuilderiusCommitPostType::POST_TYPE,
            $wpdb->posts,
            BulderiusReleasePostType::POST_TYPE
        );

        $deliverableTagsObjects = $wpdb->get_results($sqlDeliverableTags);
        $deliverableTags = [];
        foreach ($deliverableTagsObjects as $tag) {
            $deliverableTags[] = $tag->name;
        }

        return $deliverableTags;
    }
}