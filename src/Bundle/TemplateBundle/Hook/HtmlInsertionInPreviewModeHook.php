<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\Event\HtmlContainingEvent;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateHtmlContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;
use Builderius\Symfony\Component\Templating\EngineInterface;

class HtmlInsertionInPreviewModeHook extends AbstractFilter
{
    /**
     * @var BuilderiusTemplateProviderInterface
     */
    private $builderiusTemplateProvider;

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
     * @param BuilderiusTemplateProviderInterface $builderiusTemplateProvider
     * @return $this
     */
    public function setBuilderiusTemplateProvider(BuilderiusTemplateProviderInterface $builderiusTemplateProvider)
    {
        $this->builderiusTemplateProvider = $builderiusTemplateProvider;

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
     * @inheritDoc
     */
    public function getFunction()
    {
        if (!in_the_loop()) {
            return '';
        } else {
            $template = $this->builderiusTemplateProvider->getTemplate();
            if ($template && $branch = $template->getActiveBranch()) {
                if ($html = $branch->getContent(BuilderiusTemplateHtmlContentProvider::CONTENT_TYPE)) {
                    $html = do_blocks(do_shortcode($this->templatingEngine->render(
                        'BuilderiusTemplateBundle:templateDynamicContent.twig',
                        [
                            'dynamicContent' => $html
                        ]
                    )));
                    $event = new HtmlContainingEvent($html);
                    $this->eventDispatcher->dispatch($event, 'builderius_html_rendered');

                    return $event->getHtml();
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

                        return $event->getHtml();
                    }
                }
            }
        }
        
        return '';
    }
}
