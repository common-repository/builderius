<?php

namespace Builderius\MooMoo\Platform\Bundle\KernelBundle\Templating\Twig;

use Builderius\Symfony\Component\Templating\EngineInterface;
use Builderius\Symfony\Component\Templating\StreamingEngineInterface;
use Builderius\Symfony\Component\Templating\TemplateNameParserInterface;
use Builderius\Symfony\Component\Templating\TemplateReferenceInterface;
use Builderius\Twig\Environment;
use Builderius\Twig\Error\Error;
use Builderius\Twig\Error\LoaderError;
use Builderius\Twig\Loader\FilesystemLoader;
use Builderius\Twig\Template;
class TwigEngine implements \Builderius\Symfony\Component\Templating\EngineInterface, \Builderius\Symfony\Component\Templating\StreamingEngineInterface
{
    protected $environment;
    protected $parser;
    public function __construct(\Builderius\Twig\Environment $environment, \Builderius\Symfony\Component\Templating\TemplateNameParserInterface $parser)
    {
        $this->environment = $environment;
        $this->parser = $parser;
    }
    /**
     * @inheritDoc
     */
    public function render($name, array $parameters = array())
    {
        $absoluteName = $this->parser->parse($name)->getLogicalName();
        $filename = \substr(\strrchr($absoluteName, "/"), 1);
        /** @var FilesystemLoader $loader */
        $loader = $this->environment->getLoader();
        $loader->setPaths([\dirname($absoluteName)]);
        return $this->environment->render($filename, $parameters);
    }
    /**
     * @inheritDoc
     *
     * It also supports Template as name parameter.
     *
     * @throws Error if something went wrong like a thrown exception while rendering the template
     */
    public function stream($name, array $parameters = array())
    {
        $this->load($name)->display($parameters);
    }
    /**
     * @inheritDoc
     *
     * It also supports Template as name parameter.
     */
    public function exists($name)
    {
        if ($name instanceof \Builderius\Twig\Template) {
            return \true;
        }
        $loader = $this->environment->getLoader();
        if ($loader instanceof \Builderius\MooMoo\Platform\Bundle\KernelBundle\Templating\Twig\ExistsLoaderInterface || \method_exists($loader, 'exists')) {
            return $loader->exists((string) $name);
        }
        try {
            // cast possible TemplateReferenceInterface to string because the
            // EngineInterface supports them but LoaderInterface does not
            $loader->getSourceContext((string) $name)->getCode();
        } catch (\Builderius\Twig\Error\LoaderError $e) {
            return \false;
        }
        return \true;
    }
    /**
     * @inheritDoc
     *
     * It also supports Template as name parameter.
     */
    public function supports($name)
    {
        if ($name instanceof \Builderius\Twig\Template) {
            return \true;
        }
        $template = $this->parser->parse($name);
        return 'twig' === $template->get('engine');
    }
    /**
     * Loads the given template.
     *
     * @param string|TemplateReferenceInterface|Template $name A template name or an instance of
     *                                                         TemplateReferenceInterface or Template
     *
     * @return Template
     *
     * @throws \InvalidArgumentException if the template does not exist
     */
    protected function load($name)
    {
        if ($name instanceof \Builderius\Twig\Template) {
            return $name;
        }
        try {
            return $this->environment->loadTemplate((string) $name);
        } catch (\Builderius\Twig\Error\LoaderError $e) {
            throw new \InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
