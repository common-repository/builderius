<?php

namespace Builderius\Bundle\ModuleBundle\Hook;

use Builderius\Bundle\TemplateBundle\Hook\AbstractApplicantDataFilter;

class HideAdminBarForModuleApplicantDataRequestHook extends AbstractApplicantDataFilter
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
            return false;
        }

        return func_get_arg(0);
    }
}
