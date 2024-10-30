<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Event\HtmlContainingEvent;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateInterface;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateHtmlContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplatesProviderInterface;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;
use Builderius\Symfony\Component\Templating\EngineInterface;

class HtmlInsertionInHookTemplatesPreviewModeHook extends AbstractFilter
{
    /**
     * @var BuilderiusTemplatesProviderInterface
     */
    private $builderiusHookTemplatesProvider;

    /**
     * @var BuilderiusTemplateFromPostFactory
     */
    protected $builderiusTemplateFromPostFactory;

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
     * @param BuilderiusTemplatesProviderInterface $builderiusHookTemplatesProvider
     * @return $this
     */
    public function setBuilderiusHookTemplatesProvider(BuilderiusTemplatesProviderInterface $builderiusHookTemplatesProvider)
    {
        $this->builderiusHookTemplatesProvider = $builderiusHookTemplatesProvider;

        return $this;
    }

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
        $templates = $this->builderiusHookTemplatesProvider->getTemplates();

        foreach ($templates as $template) {
            if ($template->isClearExistingHooks()) {
                remove_all_actions($template->getHook());
            }
            if ('filter' === $template->getHookType()) {
                add_filter(
                    $template->getHook(),
                    function() use ($template) {
                        $this->cache->set('hook_template_resolving', true);
                        $postContentResolving = $this->cache->get('builderius_graphql_post_content_resolving');
                        if (false === $postContentResolving) {
                            $index = $this->cache->get(sprintf('hook_template_%d_index', $template->getId()));
                            if (false === $index) {
                                $index = 0;
                            } else {
                                $index = $index + 1;
                            }
                            $args = func_get_args();
                            $this->cache->set(sprintf('hook_template_args_%d_%d', $template->getId(), $index), $args);
                            $this->cache->set(sprintf('hook_template_%d_index', $template->getId()), $index);
                            $this->cache->set('hook_template_args', $args);
                            $this->cache->delete('getting_hook_args_before_hook');
                        }
                        $html = $this->getHtml($template);
                        $this->cache->delete('hook_template_resolving');
                        if (null === $html) {
                            return func_get_arg(0);
                        } else {
                            return $html;
                        }
                    },
                    $template->getSortOrder(),
                    $template->getHookAcceptedArgs()
                );
            } elseif ('action' === $template->getHookType()) {
                add_action(
                    $template->getHook(),
                    function() use ($template) {
                        $this->cache->set('hook_template_resolving', true);
                        $postContentResolving = $this->cache->get('builderius_graphql_post_content_resolving');
                        if (false === $postContentResolving) {
                            $index = $this->cache->get(sprintf('hook_template_%d_index', $template->getId()));
                            if (false === $index) {
                                $index = 0;
                            } else {
                                $index = $index + 1;
                            }
                            $args = func_get_args();
                            $this->cache->set(sprintf('hook_template_args_%d_%d', $template->getId(), $index), $args);
                            $this->cache->set(sprintf('hook_template_%d_index', $template->getId()), $index);
                            $this->cache->set('hook_template_args', $args);
                            $this->cache->delete('getting_hook_args_before_hook');
                        }
                        echo $this->getHtml($template);
                        $this->cache->delete('hook_template_resolving');
                    },
                    $template->getSortOrder(),
                    $template->getHookAcceptedArgs()
                );
            }
        }

        return $themeTemplate;
    }

    /**
     * @param BuilderiusTemplateInterface $template
     * @return string|void
     */
    public function getHtml(BuilderiusTemplateInterface $template)
    {
        $recursion = $this->cache->get('builderius_hook_templates_recursion');
        if (false === $recursion) {
            $recursion = [];
        }
        if (isset($recursion[$template->getId()])) {
            if ($recursion[$template->getId()] >= 1) {
                return null;
            }
            $recursion[$template->getId()] = $recursion[$template->getId()] + 1;
        } else {
            $recursion[$template->getId()] = 1;
        }
        $this->cache->set('builderius_hook_templates_recursion', $recursion);
        $this->cache->set('builderius_hook_template', $template);
        if ($branch = $template->getActiveBranch()) {
            if ($html = $branch->getContent(BuilderiusTemplateHtmlContentProvider::CONTENT_TYPE)) {
                $html = do_blocks(do_shortcode($this->templatingEngine->render(
                    'BuilderiusTemplateBundle:templateDynamicContent.twig',
                    [
                        'dynamicContent' => $html
                    ]
                )));
                $event = new HtmlContainingEvent($html);
                $this->eventDispatcher->dispatch($event, 'builderius_html_rendered');
                $html = $event->getHtml();
                //$this->cache->delete('builderius_hook_templates_recursion');
                $this->cache->delete('builderius_hook_template');

                return '<div class = "builderiusContent">' . $html . '</div>';
            } elseif ($commit = $branch->getActiveCommit()) {
                if ($html = $commit->getContent(BuilderiusTemplateHtmlContentProvider::CONTENT_TYPE)) {
                    $html = do_blocks(do_shortcode($this->templatingEngine->render(
                        'BuilderiusTemplateBundle:templateDynamicContent.twig',
                        [
                            'dynamicContent' => $html
                        ]
                    )));
                    $event = new HtmlContainingEvent($html);
                    $this->eventDispatcher->dispatch($event, 'builderius_html_rendered');
                    $html = $event->getHtml();
                    $this->cache->delete('builderius_hook_templates_recursion');
                    $this->cache->delete('builderius_hook_template');

                    return '<div class = "builderiusContent">' . $html . '</div>';
                }
            }
        }
    }
}
