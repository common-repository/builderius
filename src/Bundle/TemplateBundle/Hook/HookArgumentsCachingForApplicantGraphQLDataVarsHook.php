<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusPersistentObjectCache;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Symfony\Component\Cache\CacheItem;

class HookArgumentsCachingForApplicantGraphQLDataVarsHook extends AbstractApplicantDataFilter
{
    /**
     * @var BuilderiusTemplateFromPostFactory
     */
    protected $builderiusTemplateFromPostFactory;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $runtimeCache;


    /**
     * @var BuilderiusPersistentObjectCache
     */
    private $persistentCache;

    /**
     * @param BuilderiusTemplateFromPostFactory $builderiusTemplateFromPostFactory
     * @return $this
     */
    public function setBuilderiusTemplateFromPostFactory(
        BuilderiusTemplateFromPostFactory $builderiusTemplateFromPostFactory
    ){
        $this->builderiusTemplateFromPostFactory = $builderiusTemplateFromPostFactory;

        return $this;
    }

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
        $themeTemplate = func_get_arg(0);

        $user = apply_filters('builderius_get_current_user', $this->getUser());
        if (isset( $_POST['builderius-applicant-graphql-datavars']) && user_can($user, 'builderius-development')) {
            /** @var CacheItem $templateTypeCacheItem */
            $templateIdCacheItem = $this->persistentCache->getItem('applicant_graphql_template_id');
            $templateId = $templateIdCacheItem->get();
            $this->persistentCache->delete('applicant_graphql_template_id');
            $templatePost = get_post($templateId);
            if ($templatePost) {
                $template = $this->builderiusTemplateFromPostFactory->createTemplate($templatePost);
                if ($template && $template->getSubType() === 'hook') {
                    if ('filter' === $template->getHookType()) {
                        add_filter(
                            $template->getHook(),
                            function () {
                                $this->runtimeCache->set('hook_template_args', func_get_args());
                                return func_get_arg(0);
                            },
                            $template->getSortOrder(),
                            $template->getHookAcceptedArgs()
                        );
                    } elseif ('action' === $template->getHookType()) {
                        add_action(
                            $template->getHook(),
                            function () {
                                $this->runtimeCache->set('hook_template_args', func_get_args());
                            },
                            $template->getSortOrder(),
                            $template->getHookAcceptedArgs()
                        );
                    }
                }
            }
        }

        return $themeTemplate;
    }
}
