<?php

namespace Builderius\Bundle\ModuleBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;
use Builderius\Symfony\Component\Templating\EngineInterface;

class ApplicantTemplateChangingHook extends AbstractFilter
{
    /**
     * @var string
     */
    private $templatePath;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @param string $templatePath
     * @return $this
     */
    public function setTemplatePath(string $templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    /**
     * @param EngineInterface $templatingEngine
     * @return $this
     */
    public function setTemplatingEngine(EngineInterface $templatingEngine)
    {
        $this->templatingEngine = $templatingEngine;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        if (!defined( 'BUILDERIUS_DEVELOPMENT_MODE') || !BUILDERIUS_DEVELOPMENT_MODE) {
            error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
        }
        $user = apply_filters('builderius_get_current_user', $this->getUser());
        if (user_can($user, 'builderius-development') &&
            (
                isset($_POST['builderius-applicant-gbblock-data']) ||
                isset($_POST['builderius-applicant-shortcode-data'])
            )
        ) {
            echo $this->templatingEngine->render($this->templatePath);
            return null;
        } else {
            return func_get_arg(0);
        }
    }

    /**
     * @return false|\WP_User|null
     */
    protected function getUser()
    {
        $user = wp_get_current_user();
        if ($user->ID === 0 && isset($_COOKIE[LOGGED_IN_COOKIE])) {
            $userId = wp_validate_auth_cookie( $_COOKIE[LOGGED_IN_COOKIE], 'logged_in' );
            if ($userId && $userId > 0) {
                $user = get_user_by('ID', $userId);
            }
        }

        return $user;
    }
}