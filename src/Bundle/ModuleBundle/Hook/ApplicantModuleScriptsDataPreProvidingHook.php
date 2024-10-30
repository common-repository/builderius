<?php

namespace Builderius\Bundle\ModuleBundle\Hook;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Hook\AbstractApplicantDataAction;

class ApplicantModuleScriptsDataPreProvidingHook extends AbstractApplicantDataAction
{
    const CACHE_KEY = 'applicant_scripts';

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @var string
     */
    private $postParameter;

    /**
     * @param BuilderiusRuntimeObjectCache $cache
     * @return $this
     */
    public function setCache(BuilderiusRuntimeObjectCache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

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
            $wp_scripts_global_objects = func_get_arg(0);
            $cache = $this->cache->get(self::CACHE_KEY);
            if (false === $cache) {
                $cache = [];
            }
            $cache[] = $wp_scripts_global_objects;
            $this->cache->set(self::CACHE_KEY, $cache);
        }
    }
}