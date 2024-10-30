<?php

namespace Builderius\MooMoo\Platform\Bundle\CronBundle\Model;

interface CronRecurrentCommandInterface extends \Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronCommandInterface
{
    /**
     * @return string
     */
    public function getRecurrence();
}
