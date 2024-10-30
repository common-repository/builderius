<?php

namespace Builderius\Bundle\TemplateBundle\DynamicDataHelper;

class BaseDynamicDataHelpersProvider implements DynamicDataHelpersProviderInterface
{
    /**
     * @var DynamicDataHelperInterface[]
     */
    private $helpers = [];

    /**
     * @param DynamicDataHelperInterface $helper
     * @return $this
     */
    public function addHelper(DynamicDataHelperInterface $helper)
    {
        $this->helpers[$helper->getName()] = $helper;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDynamicDataHelpers()
    {
        return $this->helpers;
    }

    /**
     * @inheritDoc
     */
    public function getDynamicDataHelper($name)
    {
        if ($this->hasDynamicDataHelper($name)) {
            return $this->helpers[$name];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasDynamicDataHelper($name)
    {
        return isset($this->helpers[$name]);
    }
}