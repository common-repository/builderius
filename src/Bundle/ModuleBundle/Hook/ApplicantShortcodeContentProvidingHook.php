<?php

namespace Builderius\Bundle\ModuleBundle\Hook;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusPersistentObjectCache;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Hook\AbstractApplicantDataFilter;
use Builderius\Symfony\Component\Cache\CacheItem;

class ApplicantShortcodeContentProvidingHook extends AbstractApplicantDataFilter
{
    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $runtimeCache;

    /**
     * @var BuilderiusPersistentObjectCache
     */
    private $persistentCache;

    /**
     * @param BuilderiusRuntimeObjectCache $runtimeCache
     * @return $this
     */
    public function setRuntimeCache(BuilderiusRuntimeObjectCache $runtimeCache)
    {
        $this->runtimeCache = $runtimeCache;

        return $this;
    }

    /**
     * @param BuilderiusPersistentObjectCache $persistentCache
     * @return $this
     */
    public function setPersistentCache(BuilderiusPersistentObjectCache $persistentCache)
    {
        $this->persistentCache = $persistentCache;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $user = apply_filters('builderius_get_current_user', $this->getUser());
        if (isset($_POST['builderius-applicant-shortcode-data']) && isset($_POST['uniqid']) && user_can($user, 'builderius-development')) {
            $renderingAlready = $this->runtimeCache->get(sprintf('applicant_shortcode_%s_rendering', $_POST['uniqid']));
            if (false === $renderingAlready) {
                $this->runtimeCache->set(sprintf('applicant_shortcode_%s_rendering', $_POST['uniqid']), true);
                global $wp_query;
                $wp_query->reset_postdata();
                /** @var CacheItem $dataCacheItem */
                $dataCacheItem = $this->persistentCache->getItem(sprintf('applicant_shortcode_%s', $_POST['uniqid']));
                $shortcode = $dataCacheItem->get();
                if (null !== $shortcode) {
                    $content = do_shortcode($shortcode);
                    $this->runtimeCache->set(sprintf('applicant_shortcode_content_%s', $_POST['uniqid']), $content);

                    return $content;
                }
            }
        }

        return func_get_arg(0);
    }
}