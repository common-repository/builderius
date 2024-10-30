<?php

namespace Builderius\Bundle\TemplateBundle\Provider\TemplateAcceptableHook;

use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateAcceptableHookInterface;

class BuilderiusCompositeTemplateAcceptableHooksProvider implements BuilderiusTemplateAcceptableHooksProviderInterface
{
    /**
     * @var BuilderiusTemplateAcceptableHooksProviderInterface[]
     */
    private $providers;

    /**
     * @param BuilderiusTemplateAcceptableHooksProviderInterface $provider
     * @return $this
     */
    public function addProvider(BuilderiusTemplateAcceptableHooksProviderInterface $provider)
    {
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isAcceptable()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getAcceptableHooks()
    {
        $hooks = [];
        foreach ($this->providers as $provider) {
            if (true === $provider->isAcceptable()) {
                foreach ($provider->getAcceptableHooks() as $hook) {
                    $hooks[$hook->getName()] = $hook;
                }
            }
        }

        return $hooks;
    }
}