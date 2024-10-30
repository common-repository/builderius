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

use Builderius\Symfony\Component\Stopwatch\Stopwatch;
use Builderius\Twig\Extension\ProfilerExtension as BaseProfilerExtension;
use Builderius\Twig\Profiler\Profile;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class ProfilerExtension extends \Builderius\Twig\Extension\ProfilerExtension
{
    private $stopwatch;
    private $events;
    public function __construct(\Builderius\Twig\Profiler\Profile $profile, \Builderius\Symfony\Component\Stopwatch\Stopwatch $stopwatch = null)
    {
        parent::__construct($profile);
        $this->stopwatch = $stopwatch;
        $this->events = new \SplObjectStorage();
    }
    public function enter(\Builderius\Twig\Profiler\Profile $profile) : void
    {
        if ($this->stopwatch && $profile->isTemplate()) {
            $this->events[$profile] = $this->stopwatch->start($profile->getName(), 'template');
        }
        parent::enter($profile);
    }
    public function leave(\Builderius\Twig\Profiler\Profile $profile) : void
    {
        parent::leave($profile);
        if ($this->stopwatch && $profile->isTemplate()) {
            $this->events[$profile]->stop();
            unset($this->events[$profile]);
        }
    }
}
