<?php

namespace Builderius\MooMoo\Platform\Bundle\PostBundle\PostStatus\Registrator;

use Builderius\MooMoo\Platform\Bundle\PostBundle\PostStatus\PostStatusInterface;
interface PostStatusesRegistratorInterface
{
    /**
     * @param PostStatusInterface[] $postStatuses
     */
    public function registerPostStatuses(array $postStatuses);
}
