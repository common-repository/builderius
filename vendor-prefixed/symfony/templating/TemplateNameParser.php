<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\Templating;

/**
 * TemplateNameParser is the default implementation of TemplateNameParserInterface.
 *
 * This implementation takes everything as the template name
 * and the extension for the engine.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TemplateNameParser implements \Builderius\Symfony\Component\Templating\TemplateNameParserInterface
{
    /**
     * {@inheritdoc}
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
        return new \Builderius\Symfony\Component\Templating\TemplateReference($name, $engine);
    }
}
