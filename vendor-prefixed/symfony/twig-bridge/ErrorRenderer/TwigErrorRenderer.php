<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Bridge\Twig\ErrorRenderer;

use Builderius\Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Builderius\Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Builderius\Symfony\Component\ErrorHandler\Exception\FlattenException;
use Builderius\Symfony\Component\HttpFoundation\RequestStack;
use Builderius\Twig\Environment;
/**
 * Provides the ability to render custom Twig-based HTML error pages
 * in non-debug mode, otherwise falls back to HtmlErrorRenderer.
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class TwigErrorRenderer implements \Builderius\Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface
{
    private $twig;
    private $fallbackErrorRenderer;
    private $debug;
    /**
     * @param bool|callable $debug The debugging mode as a boolean or a callable that should return it
     */
    public function __construct(\Builderius\Twig\Environment $twig, \Builderius\Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer $fallbackErrorRenderer = null, $debug = \false)
    {
        if (!\is_bool($debug) && !\is_callable($debug)) {
            throw new \TypeError(\sprintf('Argument 3 passed to "%s()" must be a boolean or a callable, "%s" given.', __METHOD__, \is_object($debug) ? \get_class($debug) : \gettype($debug)));
        }
        $this->twig = $twig;
        $this->fallbackErrorRenderer = $fallbackErrorRenderer ?? new \Builderius\Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer();
        $this->debug = $debug;
    }
    /**
     * {@inheritdoc}
     */
    public function render(\Throwable $exception) : \Builderius\Symfony\Component\ErrorHandler\Exception\FlattenException
    {
        $exception = $this->fallbackErrorRenderer->render($exception);
        $debug = \is_bool($this->debug) ? $this->debug : ($this->debug)($exception);
        if ($debug || !($template = $this->findTemplate($exception->getStatusCode()))) {
            return $exception;
        }
        return $exception->setAsString($this->twig->render($template, ['exception' => $exception, 'status_code' => $exception->getStatusCode(), 'status_text' => $exception->getStatusText()]));
    }
    public static function isDebug(\Builderius\Symfony\Component\HttpFoundation\RequestStack $requestStack, bool $debug) : \Closure
    {
        return static function () use($requestStack, $debug) : bool {
            if (!($request = $requestStack->getCurrentRequest())) {
                return $debug;
            }
            return $debug && $request->attributes->getBoolean('showException', \true);
        };
    }
    private function findTemplate(int $statusCode) : ?string
    {
        $template = \sprintf('@Twig/Exception/error%s.html.twig', $statusCode);
        if ($this->twig->getLoader()->exists($template)) {
            return $template;
        }
        $template = '@Twig/Exception/error.html.twig';
        if ($this->twig->getLoader()->exists($template)) {
            return $template;
        }
        return null;
    }
}
