<?php

namespace Builderius\Bundle\ModuleBundle\Hook;

use Builderius\Bundle\TemplateBundle\Hook\AbstractApplicantDataAction;

class ApplicantModuleInlineAssetsDataPreProvidingStartHook extends AbstractApplicantDataAction
{
    /**
     * @var string
     */
    private $postParameter;

    /**
     * @param string $postParameter
     * @return $this
     */
    public function setPostParameter(string $postParameter)
    {
        $this->postParameter = $postParameter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $user = apply_filters('builderius_get_current_user', $this->getUser());
        if (isset( $_POST[$this->postParameter]) && user_can($user, 'builderius-development')) {
            ob_start();
        }
    }
}