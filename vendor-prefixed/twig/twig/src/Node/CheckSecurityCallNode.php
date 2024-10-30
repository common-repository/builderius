<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig\Node;

use Builderius\Twig\Compiler;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CheckSecurityCallNode extends \Builderius\Twig\Node\Node
{
    public function compile(\Builderius\Twig\Compiler $compiler)
    {
        $compiler->write("\$this->sandbox = \$this->env->getExtension('\\Builderius\\Twig\\Extension\\SandboxExtension');\n")->write("\$this->checkSecurity();\n");
    }
}
