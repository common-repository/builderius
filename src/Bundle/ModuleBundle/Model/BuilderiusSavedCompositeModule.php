<?php

namespace Builderius\Bundle\ModuleBundle\Model;

class BuilderiusSavedCompositeModule extends BuilderiusCompositeModule
{
    const ID_FIELD = 'id';

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->get(self::ID_FIELD);
    }
}