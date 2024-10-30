<?php

namespace Builderius\Bundle\TemplateBundle\Provider\TemplateAcceptableHook;

use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateAcceptableHookInterface;

interface BuilderiusTemplateAcceptableHooksProviderInterface
{
    /**
     * @return boolean
     */
    public function isAcceptable();

    /**
     * @return BuilderiusTemplateAcceptableHookInterface[]
     */
    public function getAcceptableHooks();
}