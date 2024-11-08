<?php

namespace Builderius\MooMoo\Platform\Bundle\PostBundle\PostType\Registry;

use Builderius\MooMoo\Platform\Bundle\PostBundle\PostType\PostTypeInterface;
interface PostTypesRegistryInterface
{
    /**
     * @return PostTypeInterface[]
     */
    public function getPostTypes();
    /**
     * @param string $type
     * @return PostTypeInterface|null
     */
    public function getPostType($type);
    /**
     * @param string $type
     * @return bool
     */
    public function hasPostType($type);
}
