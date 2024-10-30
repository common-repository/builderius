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

use Builderius\Symfony\Component\Yaml\Dumper as YamlDumper;
use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\TwigFilter;
/**
 * Provides integration of the Yaml component with Twig.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class YamlExtension extends \Builderius\Twig\Extension\AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters() : array
    {
        return [new \Builderius\Twig\TwigFilter('yaml_encode', [$this, 'encode']), new \Builderius\Twig\TwigFilter('yaml_dump', [$this, 'dump'])];
    }
    public function encode($input, int $inline = 0, int $dumpObjects = 0) : string
    {
        static $dumper;
        if (null === $dumper) {
            $dumper = new \Builderius\Symfony\Component\Yaml\Dumper();
        }
        if (\defined('Symfony\\Component\\Yaml\\Yaml::DUMP_OBJECT')) {
            return $dumper->dump($input, $inline, 0, $dumpObjects);
        }
        return $dumper->dump($input, $inline, 0, \false, $dumpObjects);
    }
    public function dump($value, int $inline = 0, int $dumpObjects = 0) : string
    {
        if (\is_resource($value)) {
            return '%Resource%';
        }
        if (\is_array($value) || \is_object($value)) {
            return '%' . \gettype($value) . '% ' . $this->encode($value, $inline, $dumpObjects);
        }
        return $this->encode($value, $inline, $dumpObjects);
    }
}
