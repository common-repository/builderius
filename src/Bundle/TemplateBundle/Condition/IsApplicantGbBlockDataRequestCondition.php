<?php

namespace Builderius\Bundle\TemplateBundle\Condition;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\AbstractCondition;

class IsApplicantGbBlockDataRequestCondition extends AbstractCondition
{
    /**
     * @inheritDoc
     */
    protected function getResult()
    {
        return isset( $_POST['builderius-applicant-gbblock-data']);
    }
}