<?php

namespace Builderius\MooMoo\Platform\Bundle\MenuBundle\Model;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
interface MenuElementInterface extends \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface
{
    /**
     * @return string
     */
    public function getIdentifier();
    /**
     * @return string
     */
    public function getTitle();
    /**
     * @return string
     */
    public function getParent();
}
