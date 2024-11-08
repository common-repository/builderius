<?php

namespace Builderius\MooMoo\Platform\Bundle\MetaBoxBundle\Registry;

use Builderius\MooMoo\Platform\Bundle\MetaBoxBundle\Model\MetaBoxInterface;
interface MetaBoxesRegistryInterface
{
    /**
     * @return MetaBoxInterface[]
     */
    public function getMetaBoxes();
    /**
     * @param string $id
     * @return MetaBoxInterface
     */
    public function getMetaBox($id);
    /**
     * @param string $id
     * @return bool
     */
    public function hasMetaBox($id);
}
