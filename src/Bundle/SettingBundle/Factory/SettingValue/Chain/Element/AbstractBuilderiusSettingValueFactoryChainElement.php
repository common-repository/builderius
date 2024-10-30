<?php

namespace Builderius\Bundle\SettingBundle\Factory\SettingValue\Chain\Element;

use Builderius\Bundle\SettingBundle\Factory\SettingValue\BuilderiusSettingValueFactoryInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValue;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValueInterface;
use Builderius\Bundle\SettingBundle\Model\BuilderiusSettingValuesCollection;

abstract class AbstractBuilderiusSettingValueFactoryChainElement implements BuilderiusSettingValueFactoryInterface
{
    /**
     * @var BuilderiusSettingValueFactoryInterface|null
     */
    private $successor;

    /**
     * @param BuilderiusSettingValueFactoryInterface $successor
     */
    public function setSuccessor(BuilderiusSettingValueFactoryInterface $successor)
    {
        $this->successor = $successor;
    }

    /**
     * @return BuilderiusSettingValueFactoryInterface|null
     */
    protected function getSuccessor()
    {
        return $this->successor;
    }

    /**
     * @inheritDoc
     */
    public function create(array $arguments)
    {
        if (!isset($arguments[BuilderiusSettingValue::VALUE_FIELD])) {
            throw new \Exception('Missing required argument "value" for creating BuilderiusSettingValue');
        }
        if ($this->isApplicable($arguments)) {
            return $this->createValue($arguments);
        }
        if ($this->getSuccessor()) {
            return $this->getSuccessor()->create($arguments);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function createCollection(array $arguments)
    {
        $values = [];
        foreach ($arguments as $data) {
            $settingValue = $this->create($data);
            $values[] = $settingValue;
        }

        return new BuilderiusSettingValuesCollection($values);
    }

    /**
     * @param array $arguments
     * @return BuilderiusSettingValueInterface|null
     */
    abstract protected function createValue(array $arguments);

    /**
     * @param array $arguments
     * @return boolean
     */
    abstract protected function isApplicable(array $arguments);
}
