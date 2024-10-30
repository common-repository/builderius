<?php

namespace Builderius\Bundle\TemplateBundle\Condition;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\AbstractCondition;

class IsNotApplicantGbBlockDataRequestCondition extends AbstractCondition
{
    /**
     * @inheritDoc
     */
    protected function getResult()
    {
        return isset( $_POST['builderius-applicant-shortcode-data']) ||
            isset( $_POST['builderius-applicant-data']) ||
            isset( $_POST['builderius-applicant-graphql-datavars']) ||
            isset( $_POST['builderius-applicant-template-part-data']);
    }
}