<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\DeliverableBundle\Model\BuilderiusDeliverableSubModuleInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Event\HtmlContainingEvent;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateHtmlContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModulesProviderInterface;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;
use Builderius\Symfony\Component\Templating\EngineInterface;

class HtmlInsertionInHookTemplatesFrontendModeHook extends AbstractFilter
{
    /**
     * @var DeliverableTemplateSubModulesProviderInterface
     */
    private $dhtsmsProvider;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param DeliverableTemplateSubModulesProviderInterface $dhtsmsProvider
     * @return $this
     */
    public function setBuilderiusDeliverableTemplateSubModulesProvider(
        DeliverableTemplateSubModulesProviderInterface $dhtsmsProvider
    ) {
        $this->dhtsmsProvider = $dhtsmsProvider;

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
     * @param EventDispatcher $eventDispatcher
     * @return $this
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

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
     * @inheritDoc
     */
    public function getFunction()
    {
        $themeTemplate = func_get_arg(0);
        $templates = $this->dhtsmsProvider->getTemplateSubModules();

        foreach ($templates as $templateSm) {
            $attributes = $templateSm->getAttributes();
            if (isset($attributes[BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD]) && true === $attributes[BuilderiusTemplate::CLEAR_EXISTING_HOOKS_FIELD]) {
                remove_all_actions($attributes[BuilderiusTemplate::HOOK_FIELD]);
            }
            if ('filter' === $attributes[BuilderiusTemplate::HOOK_TYPE_FIELD]) {
                add_filter(
                    $attributes[BuilderiusTemplate::HOOK_FIELD],
                    function() use ($templateSm) {
                        $this->cache->set('hook_template_resolving', true);
                        $postContentResolving = $this->cache->get('builderius_graphql_post_content_resolving');
                        if (false === $postContentResolving) {
                            $index = $this->cache->get(sprintf('dtsm_hook_template_%d_index', $templateSm->getId()));
                            if (false === $index) {
                                $index = 0;
                            } else {
                                $index = $index + 1;
                            }
                            $args = func_get_args();
                            $this->cache->set(sprintf('dtsm_hook_template_args_%d_%d', $templateSm->getId(), $index), $args);
                            $this->cache->set(sprintf('dtsm_hook_template_%d_index', $templateSm->getId()), $index);
                            $this->cache->set('hook_template_args', $args);
                            $this->cache->delete('getting_hook_args_before_hook');
                        }
                        $html = $this->getHtml($templateSm);
                        $this->cache->delete('hook_template_resolving');
                        if (null === $html) {
                            return func_get_arg(0);
                        } else {
                            return $html;
                        }
                    },
                    $attributes[BuilderiusTemplate::SORT_ORDER_FIELD],
                    $attributes[BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD]
                );
            } elseif ('action' === $attributes[BuilderiusTemplate::HOOK_TYPE_FIELD]) {
                add_action(
                    $attributes[BuilderiusTemplate::HOOK_FIELD],
                    function() use ($templateSm) {
                        $this->cache->set('hook_template_resolving', true);
                        $postContentResolving = $this->cache->get('builderius_graphql_post_content_resolving');
                        if (false === $postContentResolving) {
                            $index = $this->cache->get(sprintf('dtsm_hook_template_%d_index', $templateSm->getId()));
                            if (false === $index) {
                                $index = 0;
                            } else {
                                $index = $index + 1;
                            }
                            $args = func_get_args();
                            $this->cache->set(sprintf('dtsm_hook_template_args_%d_%d', $templateSm->getId(), $index), $args);
                            $this->cache->set(sprintf('dtsm_hook_template_%d_index', $templateSm->getId()), $index);
                            $this->cache->set('hook_template_args', $args);
                            $this->cache->delete('getting_hook_args_before_hook');
                        }
                        echo $this->getHtml($templateSm);
                        $this->cache->delete('hook_template_resolving');
                    },
                    $attributes[BuilderiusTemplate::SORT_ORDER_FIELD],
                    $attributes[BuilderiusTemplate::HOOK_ACCEPTED_ARGS_FIELD]
                );
            }
        }

        return $themeTemplate;
    }

    /**
     * @param BuilderiusDeliverableSubModuleInterface $template
     * @return string|void
     */
    public function getHtml(BuilderiusDeliverableSubModuleInterface $templateSm)
    {
        $recursion = $this->cache->get('builderius_dtsm_hook_templates_recursion');
        if (false === $recursion) {
            $recursion = [];
        }
        if (isset($recursion[$templateSm->getId()])) {
            if ($recursion[$templateSm->getId()] >= 1) {
                return null;
            }
            $recursion[$templateSm->getId()] = $recursion[$templateSm->getId()] + 1;
        } else {
            $recursion[$templateSm->getId()] = 1;
        }
        $this->cache->set('builderius_dtsm_hook_templates_recursion', $recursion);
        $this->cache->set('builderius_dtsm_hook_template', $templateSm);
        if ($builderiusHtml = $templateSm->getContent(BuilderiusTemplateHtmlContentProvider::CONTENT_TYPE)) {
            $html = do_blocks(do_shortcode($this->templatingEngine->render(
                'BuilderiusTemplateBundle:templateDynamicContent.twig',
                [
                    'dynamicContent' => $builderiusHtml
                ]
            )));
            $event = new HtmlContainingEvent($html);
            $this->eventDispatcher->dispatch($event, 'builderius_html_rendered');
            $html = $event->getHtml();
            $this->cache->delete('builderius_dtsm_hook_templates_recursion');
            $this->cache->delete('builderius_dtsm_hook_template');

            return '<div class = "builderiusContent">' . $html . '</div>';
        }
    }
}
