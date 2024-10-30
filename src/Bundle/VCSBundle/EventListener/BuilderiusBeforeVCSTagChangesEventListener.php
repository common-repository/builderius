<?php

namespace Builderius\Bundle\VCSBundle\EventListener;

use Builderius\Bundle\VCSBundle\Event\BuilderiusVCSTagEvent;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusBranchPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusCommitPostType;
use Builderius\Bundle\VCSBundle\Registration\BuilderiusVCSTagTaxonomy;

class BuilderiusBeforeVCSTagChangesEventListener
{
    public function onTagAdding(BuilderiusVCSTagEvent $event)
    {
        $commit = $event->getCommit();
        $owner = $commit->getBranch()->getOwner();
        global $wpdb;
        $sqlOwnerTags = sprintf(
            "SELECT DISTINCT t.name from %s t inner join %s tr on t.term_id = tr.term_taxonomy_id inner join %s tt on tr.term_taxonomy_id = tt.term_taxonomy_id join %s p on tr.object_id = p.ID where p.post_type = '%s' and tt.taxonomy = '%s' and p.post_parent IN (select ID from %s pp where pp.post_type = '%s' and pp.post_parent = %d) and t.name = '%s'",
            $wpdb->terms,
            $wpdb->term_relationships,
            $wpdb->term_taxonomy,
            $wpdb->posts,
            BuilderiusCommitPostType::POST_TYPE,
            BuilderiusVCSTagTaxonomy::NAME,
            $wpdb->posts,
            BuilderiusBranchPostType::POST_TYPE,
            $owner->getId(),
            $event->getTag()
        );
        $ownerTags = $wpdb->get_results($sqlOwnerTags);
        if (count($ownerTags) > 0) {
            throw new \Exception('This tag can\'t be added because it is already added to another commit');
        }
    }

    public function onTagRemoval(BuilderiusVCSTagEvent $event)
    {
        // TODO: Check if release created based on tag
    }
}