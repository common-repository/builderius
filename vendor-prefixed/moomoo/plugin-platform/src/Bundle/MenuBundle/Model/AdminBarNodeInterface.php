<?php

namespace Builderius\MooMoo\Platform\Bundle\MenuBundle\Model;

interface AdminBarNodeInterface extends \Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\MenuElementInterface
{
    /**
     * @return string
     */
    public function getHref();
    /**
     * @return bool
     */
    public function isGroup();
    /**
     * @return array
     */
    public function getMeta();
}
