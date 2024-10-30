<?php

namespace Builderius\Bundle\ModuleBundle\Model;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\InlineAssetInterface;

interface ModuleInlineAssetWithConditionInterface extends InlineAssetInterface
{
    /**
     * @return string
     */
    public function getConditionExpression();

    /**
     * @return bool
     */
    public function loadIfEmptyContext();

    /**
     * @return string
     */
    public function getContentTemplate();
}