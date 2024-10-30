<?php

namespace Builderius\Bundle\ModuleBundle\Model;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\Script;

class ModuleScriptWithCondition extends Script implements ModuleAssetWithConditionInterface
{
    const CONDITION_EXPRESSION_FIELD = 'condition_expression';
    const LOAD_IF_EMPTY_CONTEXT = 'load_if_empty_context';

    /**
     * @inheritDoc
     */
    public function getConditionExpression()
    {
        return $this->get(self::CONDITION_EXPRESSION_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function loadIfEmptyContext()
    {
        return $this->get(self::LOAD_IF_EMPTY_CONTEXT, false);
    }
}