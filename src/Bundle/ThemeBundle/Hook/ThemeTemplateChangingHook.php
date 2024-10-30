<?php

namespace Builderius\Bundle\ThemeBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;
use Builderius\Symfony\Component\Templating\EngineInterface;

class ThemeTemplateChangingHook extends AbstractFilter
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
        echo $this->templatingEngine->render($this->templatePath);
    }
}