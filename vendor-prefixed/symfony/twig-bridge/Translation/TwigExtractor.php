<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Bridge\Twig\Translation;

use Builderius\Symfony\Component\Finder\Finder;
use Builderius\Symfony\Component\Translation\Extractor\AbstractFileExtractor;
use Builderius\Symfony\Component\Translation\Extractor\ExtractorInterface;
use Builderius\Symfony\Component\Translation\MessageCatalogue;
use Builderius\Twig\Environment;
use Builderius\Twig\Error\Error;
use Builderius\Twig\Source;
/**
 * TwigExtractor extracts translation messages from a twig template.
 *
 * @author Michel Salib <michelsalib@hotmail.com>
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TwigExtractor extends \Builderius\Symfony\Component\Translation\Extractor\AbstractFileExtractor implements \Builderius\Symfony\Component\Translation\Extractor\ExtractorInterface
{
    /**
     * Default domain for found messages.
     *
     * @var string
     */
    private $defaultDomain = 'messages';
    /**
     * Prefix for found message.
     *
     * @var string
     */
    private $prefix = '';
    private $twig;
    public function __construct(\Builderius\Twig\Environment $twig)
    {
        $this->twig = $twig;
    }
    /**
     * {@inheritdoc}
     */
    public function extract($resource, \Builderius\Symfony\Component\Translation\MessageCatalogue $catalogue)
    {
        foreach ($this->extractFiles($resource) as $file) {
            try {
                $this->extractTemplate(\file_get_contents($file->getPathname()), $catalogue);
            } catch (\Builderius\Twig\Error\Error $e) {
                // ignore errors, these should be fixed by using the linter
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }
    protected function extractTemplate(string $template, \Builderius\Symfony\Component\Translation\MessageCatalogue $catalogue)
    {
        $visitor = $this->twig->getExtension('Builderius\\Symfony\\Bridge\\Twig\\Extension\\TranslationExtension')->getTranslationNodeVisitor();
        $visitor->enable();
        $this->twig->parse($this->twig->tokenize(new \Builderius\Twig\Source($template, '')));
        foreach ($visitor->getMessages() as $message) {
            $catalogue->set(\trim($message[0]), $this->prefix . \trim($message[0]), $message[1] ?: $this->defaultDomain);
        }
        $visitor->disable();
    }
    /**
     * @return bool
     */
    protected function canBeExtracted(string $file)
    {
        return $this->isFile($file) && 'twig' === \pathinfo($file, \PATHINFO_EXTENSION);
    }
    /**
     * {@inheritdoc}
     */
    protected function extractFromDirectory($directory)
    {
        $finder = new \Builderius\Symfony\Component\Finder\Finder();
        return $finder->files()->name('*.twig')->in($directory);
    }
}
