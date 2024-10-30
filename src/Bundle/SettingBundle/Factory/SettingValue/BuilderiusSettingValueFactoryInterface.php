<?php

namespace Builderius\Bundle\SettingBundle\Factory\SettingValue;

use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValuesCollectionInterface;

interface BuilderiusSettingValueFactoryInterface
{
    /**
     * @param array $arguments
     * @return BuilderiusSettingValueInterface|null
     * @throws \Exception
     */
    public function create(array $arguments);

    /**
     * @param array $arguments
     * @return BuilderiusSettingValuesCollectionInterface|null
     * @throws \Exception
     */
    public function createCollection(array $arguments);
}
