<?php

namespace Builderius\MooMoo\Platform\Bundle\CronBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;
class CronSchedule extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag implements \Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronScheduleInterface
{
    const NAME_FIELD = 'name';
    const INTERVAL_FIELD = 'interval';
    const DISPLAY_FIELD = 'display';
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getInterval()
    {
        return (int) $this->get(self::INTERVAL_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getDisplay()
    {
        return esc_html__($this->get(self::DISPLAY_FIELD));
    }
}
