<?php

namespace Builderius\MooMoo\Platform\Bundle\CronBundle\Model;

class TestCronCommand implements \Builderius\MooMoo\Platform\Bundle\CronBundle\Model\CronSingleCommandInterface
{
    /**
     * @inheritDoc
     */
    public function getTimestamp()
    {
        return \time();
    }
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'test_cron_command';
    }
    /**
     * @inheritDoc
     */
    public function getArguments()
    {
        return [];
    }
    /**
     * @inheritDoc
     */
    public function execute()
    {
        add_option('test_cron', \true);
    }
}
