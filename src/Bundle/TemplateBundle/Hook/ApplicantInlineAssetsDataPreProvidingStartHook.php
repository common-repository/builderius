<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

class ApplicantInlineAssetsDataPreProvidingStartHook extends AbstractApplicantDataAction
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $user = apply_filters('builderius_get_current_user', $this->getUser());
        if (isset( $_POST['builderius-applicant-data']) && user_can($user, 'builderius-development')) {
            ob_start();
        }
    }
}