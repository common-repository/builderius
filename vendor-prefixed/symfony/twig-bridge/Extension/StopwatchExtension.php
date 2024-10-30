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

use Builderius\Symfony\Bridge\Twig\TokenParser\StopwatchTokenParser;
use Builderius\Symfony\Component\Stopwatch\Stopwatch;
use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\TokenParser\TokenParserInterface;
/**
 * Twig extension for the stopwatch helper.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
final class StopwatchExtension extends \Builderius\Twig\Extension\AbstractExtension
{
    private $stopwatch;
    private $enabled;
    public function __construct(\Builderius\Symfony\Component\Stopwatch\Stopwatch $stopwatch = null, bool $enabled = \true)
    {
        $this->stopwatch = $stopwatch;
        $this->enabled = $enabled;
    }
    public function getStopwatch() : \Builderius\Symfony\Component\Stopwatch\Stopwatch
    {
        return $this->stopwatch;
    }
    /**
     * @return TokenParserInterface[]
     */
    public function getTokenParsers() : array
    {
        return [
            /*
             * {% stopwatch foo %}
             * Some stuff which will be recorded on the timeline
             * {% endstopwatch %}
             */
            new \Builderius\Symfony\Bridge\Twig\TokenParser\StopwatchTokenParser(null !== $this->stopwatch && $this->enabled),
        ];
    }
}
