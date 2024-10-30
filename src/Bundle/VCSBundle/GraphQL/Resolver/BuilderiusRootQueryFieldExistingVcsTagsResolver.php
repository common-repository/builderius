<?php

namespace Builderius\Bundle\VCSBundle\GraphQL\Resolver;

use Builderius\Bundle\GraphQLBundle\Executor\BuilderiusEntitiesGraphQLQueriesExecutorInterface;
use Builderius\Bundle\GraphQLBundle\Resolver\GraphQLFieldResolverInterface;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusVCSTagTaxonomy;
use Builderius\GraphQL\Type\Definition\ResolveInfo;

class BuilderiusRootQueryFieldExistingVcsTagsResolver implements GraphQLFieldResolverInterface
{
    /**
     * @var BuilderiusEntitiesGraphQLQueriesExecutorInterface
     */
    private $graphQLQueriesExecutor;

    /**
     * @param BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor
     */
    public function __construct(BuilderiusEntitiesGraphQLQueriesExecutorInterface $graphQLQueriesExecutor)
    {
        $this->graphQLQueriesExecutor = $graphQLQueriesExecutor;
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
    public function getSortOrder()
    {
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function getFieldName()
    {
        return 'existing_vcs_tags';
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
        $sqlAllTags = sprintf(
            "SELECT DISTINCT t.name, count(t.name) AS count from %s t inner join %s tt on t.term_id = tt.term_taxonomy_id left join %s tr on tr.term_taxonomy_id = t.term_id LEFT JOIN %s p on tr.object_id = p.ID WHERE tt.taxonomy = '%s' AND p.post_type = '%s' GROUP BY t.name",
            $wpdb->terms,
            $wpdb->term_taxonomy,
            $wpdb->term_relationships,
            $wpdb->posts,
            BuilderiusVCSTagTaxonomy::NAME,
            BuilderiusCommitPostType::POST_TYPE
        );

        $allTags = $wpdb->get_results($sqlAllTags);

        $sqlOwnerTags = sprintf(
            "SELECT DISTINCT t.name from %s t inner join %s tr on t.term_id = tr.term_taxonomy_id inner join %s tt on tr.term_taxonomy_id = tt.term_taxonomy_id join %s p on tr.object_id = p.ID where p.post_type = '%s' and tt.taxonomy = '%s' and p.post_parent IN (select ID from %s pp where pp.post_type = '%s' and pp.post_parent = %d) ",
            $wpdb->terms,
            $wpdb->term_relationships,
            $wpdb->term_taxonomy,
            $wpdb->posts,
            BuilderiusCommitPostType::POST_TYPE,
            BuilderiusVCSTagTaxonomy::NAME,
            $wpdb->posts,
            BuilderiusBranchPostType::POST_TYPE,
            $args['owner_id']
        );

        $ownerTags = $wpdb->get_results($sqlOwnerTags);
        $lockedTags = array_map(
            function ($a){
                return $a->name;
            },
            $ownerTags
        );
        $lockedToRemoveTags = $this->getLockedToRemoveTags();
        foreach ($allTags as $k => $tag) {
            if (in_array($tag->name, $lockedToRemoveTags)) {
                $allTags[$k]->locked_to_remove = true;
            } else {
                $allTags[$k]->locked_to_remove = false;
            }
            if (in_array($tag->name, $lockedTags)) {
                $allTags[$k]->locked_to_add = true;
             } else {
                $allTags[$k]->locked_to_add = false;
            }
        }

        return $allTags;
    }

    /**
     * @return array
     */
    private function getLockedToRemoveTags()
    {
        $queries = [
            [
                'name' => 'releases',
                'query' => 'query {
                            releases {
                                tag
                            }
                        }'
            ]
        ];
        $results = $this->graphQLQueriesExecutor->execute($queries);

        return $results['releases']['data']['releases'];
    }
}