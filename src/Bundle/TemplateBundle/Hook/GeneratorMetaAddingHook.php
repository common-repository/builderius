<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use \Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Provider\PluginsVersionsProvider;

class GeneratorMetaAddingHook extends AbstractFilter
{
    /**
     * @var PluginsVersionsProvider
     */
    private $pluginsVersionsProvider;

    /**
     * @param PluginsVersionsProvider $pluginsVersionsProvider
     * @return $this
     */
    public function setPluginsVersionsProvider(PluginsVersionsProvider $pluginsVersionsProvider)
    {
        $this->pluginsVersionsProvider = $pluginsVersionsProvider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $gen = func_get_arg(0);

        return $gen . sprintf('<meta name="generator" content="Builderius %s">', $this->pluginsVersionsProvider->getPluginVersion('builderius'));
    }
}