<?php

namespace Builderius\Bundle\TemplateBundle\Provider\TemplateAcceptableHook;

use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateAcceptableHookInterface;

class BuilderiusKadenceTemplateAcceptableHooksProvider implements BuilderiusTemplateAcceptableHooksProviderInterface
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
        $theme = wp_get_theme();

        if ( 'Kadence' === $theme->get('Name') || 'kadence' === $theme->template ) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getAcceptableHooks()
    {
        return $this->hooks;
    }
}