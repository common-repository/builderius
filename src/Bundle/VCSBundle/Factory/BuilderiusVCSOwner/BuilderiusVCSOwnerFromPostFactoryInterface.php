<?php

namespace Builderius\Bundle\VCSBundle\Factory\BuilderiusVCSOwner;

use Builderius\Bundle\VCSBundle\Model\BuilderiusVCSOwnerInterface;

interface BuilderiusVCSOwnerFromPostFactoryInterface
{
    /**
     * @param \WP_Post $post
     * @return BuilderiusVCSOwnerInterface
     */
    public function createOwner(\WP_Post $post);
}