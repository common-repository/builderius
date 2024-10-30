<?php

namespace Builderius\MooMoo\Platform\Bundle\MenuBundle\Registrator;

use Exception;
use Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\MenuElementInterface;
interface MenuElementsRegistratorInterface
{
    /**
     * @param MenuElementInterface[] $menuElements
     * @throws Exception
     */
    public function register(array $menuElements);
}
