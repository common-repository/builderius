<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig\Node;

use Builderius\Twig\Compiler;
use Builderius\Twig\Node\Expression\AbstractExpression;
use Builderius\Twig\Node\Expression\ConstantExpression;
use Builderius\Twig\Source;
/**
 * Represents a module node.
 *
 * Consider this class as being final. If you need to customize the behavior of
 * the generated class, consider adding nodes to the following nodes: display_start,
 * display_end, constructor_start, constructor_end, and class_end.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class ModuleNode extends \Builderius\Twig\Node\Node
{
    public function __construct(\Builderius\Twig\Node\Node $body, ?\Builderius\Twig\Node\Expression\AbstractExpression $parent, \Builderius\Twig\Node\Node $blocks, \Builderius\Twig\Node\Node $macros, \Builderius\Twig\Node\Node $traits, $embeddedTemplates, \Builderius\Twig\Source $source)
    {
        $nodes = ['body' => $body, 'blocks' => $blocks, 'macros' => $macros, 'traits' => $traits, 'display_start' => new \Builderius\Twig\Node\Node(), 'display_end' => new \Builderius\Twig\Node\Node(), 'constructor_start' => new \Builderius\Twig\Node\Node(), 'constructor_end' => new \Builderius\Twig\Node\Node(), 'class_end' => new \Builderius\Twig\Node\Node()];
        if (null !== $parent) {
            $nodes['parent'] = $parent;
        }
        // embedded templates are set as attributes so that they are only visited once by the visitors
        parent::__construct($nodes, ['index' => null, 'embedded_templates' => $embeddedTemplates], 1);
        // populate the template name of all node children
        $this->setSourceContext($source);
    }
    public function setIndex($index)
    {
        $this->setAttribute('index', $index);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $this->compileTemplate($compiler);
        foreach ($this->getAttribute('embedded_templates') as $template) {
            $compiler->subcompile($template);
        }
    }
    protected function compileTemplate(\Builderius\Twig\Compiler $compiler)
    {
        if (!$this->getAttribute('index')) {
            $compiler->write('<?php');
        }
        $this->compileClassHeader($compiler);
        $this->compileConstructor($compiler);
        $this->compileGetParent($compiler);
        $this->compileDisplay($compiler);
        $compiler->subcompile($this->getNode('blocks'));
        $this->compileMacros($compiler);
        $this->compileGetTemplateName($compiler);
        $this->compileIsTraitable($compiler);
        $this->compileDebugInfo($compiler);
        $this->compileGetSourceContext($compiler);
        $this->compileClassFooter($compiler);
    }
    protected function compileGetParent(\Builderius\Twig\Compiler $compiler)
    {
        if (!$this->hasNode('parent')) {
            return;
        }
        $parent = $this->getNode('parent');
        $compiler->write("protected function doGetParent(array \$context)\n", "{\n")->indent()->addDebugInfo($parent)->write('return ');
        if ($parent instanceof \Builderius\Twig\Node\Expression\ConstantExpression) {
            $compiler->subcompile($parent);
        } else {
            $compiler->raw('$this->loadTemplate(')->subcompile($parent)->raw(', ')->repr($this->getSourceContext()->getName())->raw(', ')->repr($parent->getTemplateLine())->raw(')');
        }
        $compiler->raw(";\n")->outdent()->write("}\n\n");
    }
    protected function compileClassHeader(\Builderius\Twig\Compiler $compiler)
    {
        $compiler->write("\n\nnamespace Builderius;\n\n");
        if (!$this->getAttribute('index')) {
            $compiler->write("use \\Builderius\\Twig\\Environment;\n")->write("use \\Builderius\\Twig\\Error\\LoaderError;\n")->write("use \\Builderius\\Twig\\Error\\RuntimeError;\n")->write("use \\Builderius\\Twig\\Extension\\SandboxExtension;\n")->write("use \\Builderius\\Twig\\Markup;\n")->write("use \\Builderius\\Twig\\Sandbox\\SecurityError;\n")->write("use \\Builderius\\Twig\\Sandbox\\SecurityNotAllowedTagError;\n")->write("use \\Builderius\\Twig\\Sandbox\\SecurityNotAllowedFilterError;\n")->write("use \\Builderius\\Twig\\Sandbox\\SecurityNotAllowedFunctionError;\n")->write("use \\Builderius\\Twig\\Source;\n")->write("use \\Builderius\\Twig\\Template;\n\n");
        }
        $compiler->write('/* ' . \str_replace('*/', '* /', $this->getSourceContext()->getName()) . " */\n")->write('class ' .  \substr( $compiler->getEnvironment()->getTemplateClass($this->getSourceContext()->getName(), $this->getAttribute('index')), 12 ) )->raw(" extends Template\n")->write("{\n")->indent()->write("private \$source;\n")->write("private \$macros = [];\n\n");
    }
    protected function compileConstructor(\Builderius\Twig\Compiler $compiler)
    {
        $compiler->write("public function __construct(Environment \$env)\n", "{\n")->indent()->subcompile($this->getNode('constructor_start'))->write("parent::__construct(\$env);\n\n")->write("\$this->source = \$this->getSourceContext();\n\n");
        // parent
        if (!$this->hasNode('parent')) {
            $compiler->write("\$this->parent = false;\n\n");
        }
        $countTraits = \count($this->getNode('traits'));
        if ($countTraits) {
            // traits
            foreach ($this->getNode('traits') as $i => $trait) {
                $node = $trait->getNode('template');
                $compiler->addDebugInfo($node)->write(\sprintf('$_trait_%s = $this->loadTemplate(', $i))->subcompile($node)->raw(', ')->repr($node->getTemplateName())->raw(', ')->repr($node->getTemplateLine())->raw(");\n")->write(\sprintf("if (!\$_trait_%s->isTraitable()) {\n", $i))->indent()->write("throw new RuntimeError('Template \"'.")->subcompile($trait->getNode('template'))->raw(".'\" cannot be used as a trait.', ")->repr($node->getTemplateLine())->raw(", \$this->source);\n")->outdent()->write("}\n")->write(\sprintf("\$_trait_%s_blocks = \$_trait_%s->getBlocks();\n\n", $i, $i));
                foreach ($trait->getNode('targets') as $key => $value) {
                    $compiler->write(\sprintf('if (!isset($_trait_%s_blocks[', $i))->string($key)->raw("])) {\n")->indent()->write("throw new RuntimeError('Block ")->string($key)->raw(' is not defined in trait ')->subcompile($trait->getNode('template'))->raw(".', ")->repr($node->getTemplateLine())->raw(", \$this->source);\n")->outdent()->write("}\n\n")->write(\sprintf('$_trait_%s_blocks[', $i))->subcompile($value)->raw(\sprintf('] = $_trait_%s_blocks[', $i))->string($key)->raw(\sprintf(']; unset($_trait_%s_blocks[', $i))->string($key)->raw("]);\n\n");
                }
            }
            if ($countTraits > 1) {
                $compiler->write("\$this->traits = array_merge(\n")->indent();
                for ($i = 0; $i < $countTraits; ++$i) {
                    $compiler->write(\sprintf('$_trait_%s_blocks' . ($i == $countTraits - 1 ? '' : ',') . "\n", $i));
                }
                $compiler->outdent()->write(");\n\n");
            } else {
                $compiler->write("\$this->traits = \$_trait_0_blocks;\n\n");
            }
            $compiler->write("\$this->blocks = array_merge(\n")->indent()->write("\$this->traits,\n")->write("[\n");
        } else {
            $compiler->write("\$this->blocks = [\n");
        }
        // blocks
        $compiler->indent();
        foreach ($this->getNode('blocks') as $name => $node) {
            $compiler->write(\sprintf("'%s' => [\$this, 'block_%s'],\n", $name, $name));
        }
        if ($countTraits) {
            $compiler->outdent()->write("]\n")->outdent()->write(");\n");
        } else {
            $compiler->outdent()->write("];\n");
        }
        $compiler->subcompile($this->getNode('constructor_end'))->outdent()->write("}\n\n");
    }
    protected function compileDisplay(\Builderius\Twig\Compiler $compiler)
    {
        $compiler->write("protected function doDisplay(array \$context, array \$blocks = [])\n", "{\n")->indent()->write("\$macros = \$this->macros;\n")->subcompile($this->getNode('display_start'))->subcompile($this->getNode('body'));
        if ($this->hasNode('parent')) {
            $parent = $this->getNode('parent');
            $compiler->addDebugInfo($parent);
            if ($parent instanceof \Builderius\Twig\Node\Expression\ConstantExpression) {
                $compiler->write('$this->parent = $this->loadTemplate(')->subcompile($parent)->raw(', ')->repr($this->getSourceContext()->getName())->raw(', ')->repr($parent->getTemplateLine())->raw(");\n");
                $compiler->write('$this->parent');
            } else {
                $compiler->write('$this->getParent($context)');
            }
            $compiler->raw("->display(\$context, array_merge(\$this->blocks, \$blocks));\n");
        }
        $compiler->subcompile($this->getNode('display_end'))->outdent()->write("}\n\n");
    }
    protected function compileClassFooter(\Builderius\Twig\Compiler $compiler)
    {
        $compiler->subcompile($this->getNode('class_end'))->outdent()->write("}\n");
    }
    protected function compileMacros(\Builderius\Twig\Compiler $compiler)
    {
        $compiler->subcompile($this->getNode('macros'));
    }
    protected function compileGetTemplateName(\Builderius\Twig\Compiler $compiler)
    {
        $compiler->write("public function getTemplateName()\n", "{\n")->indent()->write('return ')->repr($this->getSourceContext()->getName())->raw(";\n")->outdent()->write("}\n\n");
    }
    protected function compileIsTraitable(\Builderius\Twig\Compiler $compiler)
    {
        // A template can be used as a trait if:
        //   * it has no parent
        //   * it has no macros
        //   * it has no body
        //
        // Put another way, a template can be used as a trait if it
        // only contains blocks and use statements.
        $traitable = !$this->hasNode('parent') && 0 === \count($this->getNode('macros'));
        if ($traitable) {
            if ($this->getNode('body') instanceof \Builderius\Twig\Node\BodyNode) {
                $nodes = $this->getNode('body')->getNode(0);
            } else {
                $nodes = $this->getNode('body');
            }
            if (!\count($nodes)) {
                $nodes = new \Builderius\Twig\Node\Node([$nodes]);
            }
            foreach ($nodes as $node) {
                if (!\count($node)) {
                    continue;
                }
                if ($node instanceof \Builderius\Twig\Node\TextNode && \ctype_space($node->getAttribute('data'))) {
                    continue;
                }
                if ($node instanceof \Builderius\Twig\Node\BlockReferenceNode) {
                    continue;
                }
                $traitable = \false;
                break;
            }
        }
        if ($traitable) {
            return;
        }
        $compiler->write("public function isTraitable()\n", "{\n")->indent()->write(\sprintf("return %s;\n", $traitable ? 'true' : 'false'))->outdent()->write("}\n\n");
    }
    protected function compileDebugInfo(\Builderius\Twig\Compiler $compiler)
    {
        $compiler->write("public function getDebugInfo()\n", "{\n")->indent()->write(\sprintf("return %s;\n", \str_replace("\n", '', \var_export(\array_reverse($compiler->getDebugInfo(), \true), \true))))->outdent()->write("}\n\n");
    }
    protected function compileGetSourceContext(\Builderius\Twig\Compiler $compiler)
    {
        $compiler->write("public function getSourceContext()\n", "{\n")->indent()->write('return new Source(')->string($compiler->getEnvironment()->isDebug() ? $this->getSourceContext()->getCode() : '')->raw(', ')->string($this->getSourceContext()->getName())->raw(', ')->string($this->getSourceContext()->getPath())->raw(");\n")->outdent()->write("}\n");
    }
    protected function compileLoadTemplate(\Builderius\Twig\Compiler $compiler, $node, $var)
    {
        if ($node instanceof \Builderius\Twig\Node\Expression\ConstantExpression) {
            $compiler->write(\sprintf('%s = $this->loadTemplate(', $var))->subcompile($node)->raw(', ')->repr($node->getTemplateName())->raw(', ')->repr($node->getTemplateLine())->raw(");\n");
        } else {
            throw new \LogicException('Trait templates can only be constant nodes.');
        }
    }
}
