<?php

namespace Builderius\Bundle\ACFBundle\GraphQL\Resolver;

class ACFGroupFieldUserValueResolver extends ACFRepeaterRowUserValueResolver
{
    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['AcfGroupField'];
    }
}