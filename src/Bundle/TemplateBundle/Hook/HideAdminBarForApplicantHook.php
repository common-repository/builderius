<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

class HideAdminBarForApplicantHook extends AbstractApplicantDataFilter
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $user = apply_filters('builderius_get_current_user', $this->getUser());
        if (isset( $_POST['builderius-applicant-data']) && user_can($user, 'builderius-development')) {
            return false;
        }

        return func_get_arg(0);
    }
}
