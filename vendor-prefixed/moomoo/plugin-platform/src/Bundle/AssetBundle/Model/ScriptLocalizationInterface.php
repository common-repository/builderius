<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Model;

interface ScriptLocalizationInterface
{
    /**
     * @return string
     */
    public function getObjectName();
    /**
     * @return string
     */
    public function getPropertyName();
    /**
     * @return mixed
     */
    public function getPropertyData();

    public function getSortOrder();
}
