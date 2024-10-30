<?php

namespace Builderius\Bundle\TemplateBundle\DynamicDataHelper;

class CompositeDynamicDataHelpersProvider implements DynamicDataHelpersProviderInterface
{
    /**
     * @var DynamicDataHelpersProviderInterface[]
     */
    private $providers = [];

    /**
     * @var DynamicDataHelperInterface[]
     */
    private $helpers = [];

    public function addProvider(DynamicDataHelpersProviderInterface $provider)
    {
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDynamicDataHelpers()
    {
        if (empty($this->helpers)) {
            foreach ($this->providers as $provider) {
                foreach ($provider->getDynamicDataHelpers() as $helper) {
                    $this->helpers[$helper->getName()] = $helper;
                }
            }
        }

        return $this->helpers;
    }

    /**
     * @inheritDoc
     */
    public function getDynamicDataHelper($name)
    {
        if ($this->hasDynamicDataHelper($name)) {
            return $this->getDynamicDataHelpers()[$name];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasDynamicDataHelper($name)
    {
        return isset($this->getDynamicDataHelpers()[$name]);
    }
}