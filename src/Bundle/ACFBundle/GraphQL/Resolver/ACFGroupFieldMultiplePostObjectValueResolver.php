<?php

namespace Builderius\Bundle\ACFBundle\GraphQL\Resolver;

class ACFGroupFieldMultiplePostObjectValueResolver extends ACFRepeaterRowMultiplePostObjectValueResolver
{
    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['AcfGroupField'];
    }
}