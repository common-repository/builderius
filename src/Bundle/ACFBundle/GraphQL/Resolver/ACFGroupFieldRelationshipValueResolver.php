<?php

namespace Builderius\Bundle\ACFBundle\GraphQL\Resolver;

class ACFGroupFieldRelationshipValueResolver extends ACFRepeaterRowRelationshipValueResolver
{
    /**
     * @inheritDoc
     */
    public function getTypeNames()
    {
        return ['AcfGroupField'];
    }
}