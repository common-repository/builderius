<?php

namespace Builderius\MooMoo\Platform\Bundle\PostBundle\PostType\Registrator;

use Builderius\MooMoo\Platform\Bundle\PostBundle\PostType\PostTypeInterface;
interface PostTypesRegistratorInterface
{
    /**
     * @param PostTypeInterface[] $postTypes
     */
    public function registerPostTypes(array $postTypes);
}
