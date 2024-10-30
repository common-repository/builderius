<?php

namespace Builderius\Bundle\ACFBundle\GraphQL\Resolver;

class ACFGroupFieldRepeaterValueResolver extends ACFRepeaterRowRepeaterValueResolver
{
    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['AcfGroupField'];
    }
}