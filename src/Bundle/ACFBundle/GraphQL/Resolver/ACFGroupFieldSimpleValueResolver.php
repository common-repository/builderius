<?php

namespace Builderius\Bundle\ACFBundle\GraphQL\Resolver;

class ACFGroupFieldSimpleValueResolver extends ACFRepeaterRowSimpleValueResolver
{
    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['AcfGroupField'];
    }
}