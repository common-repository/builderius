<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig\Extension;

use Builderius\Twig\NodeVisitor\SandboxNodeVisitor;
use Builderius\Twig\Sandbox\SecurityNotAllowedMethodError;
use Builderius\Twig\Sandbox\SecurityNotAllowedPropertyError;
use Builderius\Twig\Sandbox\SecurityPolicyInterface;
use Builderius\Twig\Source;
use Builderius\Twig\TokenParser\SandboxTokenParser;
final class SandboxExtension extends \Builderius\Twig\Extension\AbstractExtension
{
    private $sandboxedGlobally;
    private $sandboxed;
    private $policy;
    public function __construct(\Builderius\Twig\Sandbox\SecurityPolicyInterface $policy, $sandboxed = \false)
    {
        $this->policy = $policy;
        $this->sandboxedGlobally = $sandboxed;
    }
    public function getTokenParsers() : array
    {
        return [new \Builderius\Twig\TokenParser\SandboxTokenParser()];
    }
    public function getNodeVisitors() : array
    {
        return [new \Builderius\Twig\NodeVisitor\SandboxNodeVisitor()];
    }
    public function enableSandbox() : void
    {
        $this->sandboxed = \true;
    }
    public function disableSandbox() : void
    {
        $this->sandboxed = \false;
    }
    public function isSandboxed() : bool
    {
        return $this->sandboxedGlobally || $this->sandboxed;
    }
    public function isSandboxedGlobally() : bool
    {
        return $this->sandboxedGlobally;
    }
    public function setSecurityPolicy(\Builderius\Twig\Sandbox\SecurityPolicyInterface $policy)
    {
        $this->policy = $policy;
    }
    public function getSecurityPolicy() : \Builderius\Twig\Sandbox\SecurityPolicyInterface
    {
        return $this->policy;
    }
    public function checkSecurity($tags, $filters, $functions) : void
    {
        if ($this->isSandboxed()) {
            $this->policy->checkSecurity($tags, $filters, $functions);
        }
    }
    public function checkMethodAllowed($obj, $method, int $lineno = -1, \Builderius\Twig\Source $source = null) : void
    {
        if ($this->isSandboxed()) {
            try {
                $this->policy->checkMethodAllowed($obj, $method);
            } catch (\Builderius\Twig\Sandbox\SecurityNotAllowedMethodError $e) {
                $e->setSourceContext($source);
                $e->setTemplateLine($lineno);
                throw $e;
            }
        }
    }
    public function checkPropertyAllowed($obj, $property, int $lineno = -1, \Builderius\Twig\Source $source = null) : void
    {
        if ($this->isSandboxed()) {
            try {
                $this->policy->checkPropertyAllowed($obj, $property);
            } catch (\Builderius\Twig\Sandbox\SecurityNotAllowedPropertyError $e) {
                $e->setSourceContext($source);
                $e->setTemplateLine($lineno);
                throw $e;
            }
        }
    }
    public function ensureToStringAllowed($obj, int $lineno = -1, \Builderius\Twig\Source $source = null)
    {
        if ($this->isSandboxed() && \is_object($obj) && \method_exists($obj, '__toString')) {
            try {
                $this->policy->checkMethodAllowed($obj, '__toString');
            } catch (\Builderius\Twig\Sandbox\SecurityNotAllowedMethodError $e) {
                $e->setSourceContext($source);
                $e->setTemplateLine($lineno);
                throw $e;
            }
        }
        return $obj;
    }
}
