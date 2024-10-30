<?php

namespace Builderius\Bundle\TemplateBundle\Provider\TemplateAcceptableHook;

use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateAcceptableHookInterface;

class BuilderiusWpCoreTemplateAcceptableHooksProvider implements BuilderiusTemplateAcceptableHooksProviderInterface
{
    /**
     * @var BuilderiusTemplateAcceptableHookInterface[]
     */
    private $hooks = [];

    /**
     * @param BuilderiusTemplateAcceptableHookInterface $hook
     * @return $this
     */
    public function addHook(BuilderiusTemplateAcceptableHookInterface $hook)
    {
        $this->hooks[] = $hook;

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
        return $this->hooks;
    }
}