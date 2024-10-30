<?php

namespace Builderius\Bundle\SettingBundle\Model;

use Builderius\Bundle\BuilderBundle\Model\BuilderiusBuilderFormInterface;
use Builderius\Bundle\BuilderBundle\Model\BuilderiusBuilderFormTabInterface;
use Builderius\Bundle\CategoryBundle\Model\BuilderiusCategoryInterface;

interface BuilderiusSettingPathInterface
{
    /**
     * @return string
     */
    public function getName();
    
    /**
     * @return BuilderiusBuilderFormInterface
     */
    public function getForm();

    /**
     * @return BuilderiusBuilderFormTabInterface
     */
    public function getTab();

    /**
     * @return BuilderiusCategoryInterface
     */
    public function getCategory();
}
