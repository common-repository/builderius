<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\Event\HtmlContainingEvent;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateHtmlContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\DeliverableTemplateSubModule\DeliverableTemplateSubModuleProviderInterface;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\EventDispatcher;
use Builderius\Symfony\Component\Templating\EngineInterface;

class HtmlInsertionInFrontendModeHook extends AbstractFilter
{
    /**
     * @var DeliverableTemplateSubModuleProviderInterface
     */
    private $dtsmProvider;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param DeliverableTemplateSubModuleProviderInterface $dtsmProvider
     * @return $this
     */
    public function setBuilderiusDeliverableTemplateSubModuleProvider(
        DeliverableTemplateSubModuleProviderInterface $dtsmProvider
    ) {
        $this->dtsmProvider = $dtsmProvider;

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
        $html = func_get_arg(0);
        if (!in_the_loop()) {
            return $html;
        } else {
            $templateSm = $this->dtsmProvider->getTemplateSubModule();
            if ($templateSm) {
                if ($builderiusHtml = $templateSm->getContent(BuilderiusTemplateHtmlContentProvider::CONTENT_TYPE)) {
                    $html = do_blocks(do_shortcode($this->templatingEngine->render(
                        'BuilderiusTemplateBundle:templateDynamicContent.twig',
                        [
                            'dynamicContent' => $builderiusHtml
                        ]
                    )));
                    $event = new HtmlContainingEvent($html);
                    $this->eventDispatcher->dispatch($event, 'builderius_html_rendered');

                    return $event->getHtml();
                }
            }
        }
        
        return $html;
    }
}
