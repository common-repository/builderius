<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Bridge\Twig\Extension;

use Builderius\Symfony\Bridge\Twig\NodeVisitor\TranslationDefaultDomainNodeVisitor;
use Builderius\Symfony\Bridge\Twig\NodeVisitor\TranslationNodeVisitor;
use Builderius\Symfony\Bridge\Twig\TokenParser\TransDefaultDomainTokenParser;
use Builderius\Symfony\Bridge\Twig\TokenParser\TransTokenParser;
use Builderius\Symfony\Contracts\Translation\TranslatorInterface;
use Builderius\Symfony\Contracts\Translation\TranslatorTrait;
use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\NodeVisitor\NodeVisitorInterface;
use Builderius\Twig\TwigFilter;
// Help opcache.preload discover always-needed symbols
\class_exists(\Builderius\Symfony\Contracts\Translation\TranslatorInterface::class);
/**
 * Provides integration of the Translation component with Twig.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class TranslationExtension extends \Builderius\Twig\Extension\AbstractExtension
{
    private $translator;
    private $translationNodeVisitor;
    public function __construct(\Builderius\Symfony\Contracts\Translation\TranslatorInterface $translator = null, \Builderius\Twig\NodeVisitor\NodeVisitorInterface $translationNodeVisitor = null)
    {
        $this->translator = $translator;
        $this->translationNodeVisitor = $translationNodeVisitor;
    }
    public function getTranslator() : \Builderius\Symfony\Contracts\Translation\TranslatorInterface
    {
        if (null === $this->translator) {
            if (!\interface_exists(\Builderius\Symfony\Contracts\Translation\TranslatorInterface::class)) {
                throw new \LogicException(\sprintf('You cannot use the "%s" if the Translation Contracts are not available. Try running "composer require symfony/translation".', __CLASS__));
            }
            $this->translator = new class implements \Builderius\Symfony\Contracts\Translation\TranslatorInterface
            {
                use TranslatorTrait;
            };
        }
        return $this->translator;
    }
    /**
     * {@inheritdoc}
     */
    public function getFilters() : array
    {
        return [new \Builderius\Twig\TwigFilter('trans', [$this, 'trans'])];
    }
    /**
     * {@inheritdoc}
     */
    public function getTokenParsers() : array
    {
        return [
            // {% trans %}Symfony is great!{% endtrans %}
            new \Builderius\Symfony\Bridge\Twig\TokenParser\TransTokenParser(),
            // {% trans_default_domain "foobar" %}
            new \Builderius\Symfony\Bridge\Twig\TokenParser\TransDefaultDomainTokenParser(),
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors() : array
    {
        return [$this->getTranslationNodeVisitor(), new \Builderius\Symfony\Bridge\Twig\NodeVisitor\TranslationDefaultDomainNodeVisitor()];
    }
    public function getTranslationNodeVisitor() : \Builderius\Symfony\Bridge\Twig\NodeVisitor\TranslationNodeVisitor
    {
        return $this->translationNodeVisitor ?: ($this->translationNodeVisitor = new \Builderius\Symfony\Bridge\Twig\NodeVisitor\TranslationNodeVisitor());
    }
    public function trans(string $message, array $arguments = [], string $domain = null, string $locale = null, int $count = null) : string
    {
        if (null !== $count) {
            $arguments['%count%'] = $count;
        }
        return $this->getTranslator()->trans($message, $arguments, $domain, $locale);
    }
}
