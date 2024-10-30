<?php

namespace Builderius\MooMoo\Platform\Bundle\KernelBundle\Templating;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Kernel\Kernel;
use Builderius\Symfony\Component\DependencyInjection\ContainerInterface;
use Builderius\Symfony\Component\Templating\TemplateNameParserInterface;
use Builderius\Symfony\Component\Templating\TemplateReference;
use Builderius\Symfony\Component\Templating\TemplateReferenceInterface;
class TemplateNameParser implements \Builderius\Symfony\Component\Templating\TemplateNameParserInterface
{
    /**
     * @var Kernel
     */
    private $kernel;
    /**
     * @param ContainerInterface $container
     */
    public function __construct(\Builderius\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->kernel = $container->get('kernel');
    }
    /**
     * @inheritDoc
     */
    public function parse($name)
    {
        if ($name instanceof \Builderius\Symfony\Component\Templating\TemplateReferenceInterface) {
            return $name;
        }
        $engine = null;
        if (\false !== ($pos = \strrpos($name, '.'))) {
            $engine = \substr($name, $pos + 1);
        }
        $bundleName = null;
        if (\false !== ($pos = \strrpos($name, ':'))) {
            $bundleName = \substr($name, 0, $pos);
            $name = \substr($name, $pos + 1);
        }
        if ($bundleName) {
            if ($bundle = $this->kernel->getBundle($bundleName)) {
                $name = \sprintf('%s/Resources/views/%s', $bundle->getPath(), $name);
            }
        }
        return new \Builderius\Symfony\Component\Templating\TemplateReference($name, $engine);
    }
}
