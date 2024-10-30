<?php

namespace Builderius\MooMoo\Platform\Bundle\MenuBundle\Registry;

use Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\MenuElementInterface;
interface MenuElementsRegistryInterface
{
    /**
     * @return MenuElementInterface[]
     */
    public function getMenuElements();
    /**
     * @param string $identifier
     * @return MenuElementInterface
     */
    public function getMenuElement($identifier);
    /**
     * @param string $identifier
     * @return bool
     */
    public function hasMenuElement($identifier);
}
