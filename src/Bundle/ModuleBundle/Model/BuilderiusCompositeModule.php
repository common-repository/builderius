<?php

namespace Builderius\Bundle\ModuleBundle\Model;

class BuilderiusCompositeModule extends BuilderiusModule implements BuilderiusCompositeModuleInterface
{
    const CONFIG_FIELD = 'config';

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->get(self::CONFIG_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $config)
    {
        $this->set(self::CONFIG_FIELD, $config);

        return $this;
    }
}
