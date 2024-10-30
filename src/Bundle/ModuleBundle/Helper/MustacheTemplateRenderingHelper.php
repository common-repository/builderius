<?php

namespace Builderius\Bundle\ModuleBundle\Helper;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\Mustache\Cache\FilesystemCache;
use Builderius\Mustache\Engine;

class MustacheTemplateRenderingHelper
{
    /**
     * @var array
     */
    private $conditions = [];

    /**
     * @var array
     */
    private $partials = [];

    /**
     * @var array
     */
    private $specs = [];

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * @param ExpressionLanguage $expressionLanguage
     */
    public function __construct(
        ExpressionLanguage $expressionLanguage
    ) {
        $this->expressionLanguage = $expressionLanguage;
    }

    /**
     * @param $mustacheTemplateNode
     * @param array $dataContent
     * @param array $globals
     */
    public function render($mustacheTemplateNode, array $dataContent, array $globals = null)
    {
        $this->partials = [];
        $traversed = $this->traverseTmpl($mustacheTemplateNode);
        $conditions = [];
        foreach ($this->partials as $name => $parts) {
            $conditions[$name] = implode('', $parts);
        }
        $dataExprRegex = '/(data-expression+(-+[a-z]+)*=(\'|"){{(.*?)}}(\'|")|data-expression+(-+[a-z]+)*=(\'|"){{{(.*?)}}}(\'|"))/';
        $dataVarRegex = '/[^{]{{[^{].*?[^}]}}[^}]|[^{]{{{[^{].*?[^}]}}}[^}]/';
        if (!empty($conditions)) {
            foreach ($conditions as $k => $condition) {
                preg_match($dataExprRegex, $condition, $matched);
                if (!empty($matched)) {
                    preg_match($dataVarRegex, $matched[0], $submatched);
                    if (!empty($submatched)) {
                        $submatched = $submatched[0];
                        $first = substr($submatched, 0, 1);
                        if ('{' !== $first) {
                            $submatched = ltrim($submatched, $submatched[0]);
                        }
                        $last = substr($submatched, -1);
                        if ('}' !== $last) {
                            $submatched = rtrim($submatched, $last);
                        }
                        $conditions[$k] = str_replace($matched[0], $submatched, $condition);
                    }
                }
            }
        }

        preg_match($dataExprRegex, $traversed, $matched2);
        if (!empty($matched2)) {
            preg_match($dataVarRegex, $matched2[0], $submatched2);
            if (!empty($submatched2)) {
                $submatched2 = $submatched2[0];
                $first = substr($submatched2, 0, 1);
                if ('{' !== $first) {
                    $submatched2 = ltrim($submatched2, $submatched2[0]);
                }
                $last = substr($submatched2, -1);
                if ('}' !== $last) {
                    $submatched2 = rtrim($submatched2, $last);
                }
                $traversed = str_replace($matched2[0], $submatched2, $traversed);
            }
        }

        $fsCache = new FilesystemCache(sprintf('%s/builderius/cache/mustache-templates/', wp_upload_dir()['basedir']));

        $m = new Engine(['cache' => $fsCache]);
        $m->setExpressionLanguage($this->expressionLanguage);
        $m->setPartials($conditions);
        if (null === $globals) {
            $globals = $this->specs;
        } else {
            $globals = array_merge($globals, $this->specs);
        }

        return $m->render($traversed, $this->preprocessContext($dataContent), $globals);
    }

    /**
     * @param array $context
     * @return array
     */
    protected function preprocessContext(array $context)
    {
        foreach ($context as $index => $value) {
            if (is_array($value)) {
                if (!isset($value['index'])) {
                    $context[$index]['index'] = $index;
                }
                foreach ($value as $key => $subvalue) {
                    if (is_array($subvalue)) {
                        if (isset($subvalue[0])) {
                            $context[$index][$key] = $this->preprocessContext($subvalue);
                        }
                    }
                }
            } elseif(is_object($value) && !property_exists($value, 'index')) {
                $value->index = $index;
            }
        }

        return $context;
    }

    /**
     * @param $targetNode
     * @return string
     */
    protected function traverseTmpl($targetNode)
    {
        $onlyTagRegex = '/<[a-zA-Z]+[a-zA-Z-_:0-9]*( [a-zA-Z-_:0-9]+=(\'|").+(\'|"))*\/? *>/';
        $nodeName = $targetNode->nodeName;
        $data = [];
        $conditionVar = null;
        $cloned = null;
        $openingTag = '';

        if ($targetNode instanceof \DOMElement) {
            $source = $targetNode->getAttribute('data-source') ? : ($targetNode->getAttribute('data-section') ? : ($targetNode->getAttribute('data-when') ? : null));
            $condition = $targetNode->getAttribute('data-condition') ? : ($targetNode->getAttribute('data-partial') ? : ($targetNode->getAttribute('data-if') ? : null));
            $recursiveName = $targetNode->getAttribute('data-recursive-name') ? : ($targetNode->getAttribute('data-partial-name') ? : ($targetNode->getAttribute('data-if-name') ? : null));

            if (!in_array($nodeName, ['builderius-collection', 'builderius-collection-ul', 'template'])) {
                $cloned = clone $targetNode;
            }

            $children = $targetNode->childNodes;

            //before
            /*if ('builderius-collection' === $nodeName) {

            } else */if ('template' === $nodeName && $targetNode->nodeName !== 'builderius-dynamic-table') {
                if (strpos($targetNode->getAttribute('class'), 'uniParentTmpl') !== false) {
                    $data[] = '{{#.}}';
                } else if (!is_null($condition) || !is_null($recursiveName)) {
                    $conditionVar = !is_null($recursiveName) ? $recursiveName : ('a' . md5($targetNode->ownerDocument->saveHTML($targetNode)));
                    if (!is_null($condition)) {
                        $data[] = sprintf('{{> %s ? "%s" : ""}}', $condition, $conditionVar);
                    } else {
                        $data[] = sprintf('{{> %s}}', $conditionVar);
                    }
                } else if (!is_null($source)) {
                    $data[] = sprintf('{{#%s}}', $source);
                }
            } else {
                if (in_array($nodeName, ['builderius-fancy-select']) && $cloned) {
                    $copiedInnerHtml = $this->getInnerHTML($cloned);
                    $this->setInnerHTML($cloned, '');
                    $copiedOuterHtml = $this->getOuterHTML($cloned);
                    preg_match($onlyTagRegex, $copiedOuterHtml, $matched);
                    $openingTag = !empty($matched) ? $matched[0] : '';
                    $data[] = $openingTag;
                    $data[] = '{{=<%% %%>=}}';
                    $data[] = $copiedInnerHtml;
                    $children = [];
                } else if (!empty($children) && $cloned) {
                    $isAttr = $cloned->getAttribute('is') ? : null;;

                    if ($isAttr && strpos($isAttr, 'builderius') !== false) {
                        foreach ($cloned->attributes as $a) {
                            if (!in_array($a->name, ['data-content', 'data-globals', 'data-tab', 'data-tab-panel'])) {
                                preg_match('/{{.*?}}/', $a->value, $submatched);
                                if (!empty($submatched)) {
                                    $parti = 'a' . md5($a->value);
                                    $this->specs[$parti] = $a->value;
                                    $cloned->setAttribute($a->name, sprintf('{{%s}}', $parti));
                                }
                            }
                        }
                    }
                    $this->setInnerHTML($cloned, '');
                    $copiedOuterHtml = $this->getOuterHTML($cloned);
                    preg_match($onlyTagRegex, $copiedOuterHtml, $matched);
                    $openingTag = !empty($matched) ? $matched[0] : '';
                    $data[] = $openingTag;
                } else {
                    $data[] = $this->getOuterHTML($targetNode);
                }
            }

            foreach ($children as $child) {
                $childData = $this->traverseTmpl($child);
                if (!is_null($condition) && !is_null($source)) {
                    $childData = sprintf('{{#%s}}%s{{/%s}}', $source, $childData, $source);
                }
                if (!is_null($conditionVar)) {
                    if (!isset($this->partials[$conditionVar]) || is_null($this->partials[$conditionVar])) {
                        $this->partials[$conditionVar] = [];
                    }
                    $this->partials[$conditionVar][] = $childData;
                } else {
                    $data[] = $childData;
                }
            }

            //after
            if ('template' === $nodeName) {
                if (strpos($targetNode->getAttribute('class'), 'uniParentTmpl') !== false) {
                    $data[] = '{{/.}}';
                } else if (!is_null($source) && is_null($condition)) {
                    $data[] = sprintf('{{/%s}}', $source);
                }
            } else {
                if (in_array($nodeName, ['builderius-fancy-select']) && $cloned) {
                    $data[] = '<%%={{ }}=%%>';
                    $this->setInnerHTML($cloned, '');
                    $copiedOuterHtml = $this->getOuterHTML($cloned);
                    $arr = explode($openingTag, $copiedOuterHtml);
                    $closingTag = $arr[1];
                    $data[] = $closingTag;
                } else if (!empty($children) && $cloned) {
                    $this->setInnerHTML($cloned, '');
                    $copiedOuterHtml = $this->getOuterHTML($cloned);
                    $arr = explode($openingTag, $copiedOuterHtml);
                    $closingTag = $arr[1];
                    $data[] = $closingTag;
                }
            }
        } else if ($targetNode instanceof \DOMText) {
            $data[] = $targetNode->nodeValue;
        }

        return str_replace(
            "%7B%7B",
            "{{",
            str_replace(
                "%7D%7D",
                "}}",
                str_replace(
                    "%7B%7B%7B",
                    "{{{",
                    str_replace(
                        "%7D%7D%7D",
                        "}}}",
                        implode('', $data)
                    )
                )
            )
        );

    }

    /**
     * @param \DOMElement $element
     * @return string
     */
    private function getInnerHTML(\DOMElement $element)
    {
        $doc = $element->ownerDocument;

        $html = '';

        foreach ($element->childNodes as $node) {
            $html .= $doc->saveHTML($node);
        }

        return $html;
    }

    /**
     * @param \DOMElement $element
     * @param string $html
     */
    private function setInnerHTML(\DOMElement $element, $html)
    {
        while ($element->hasChildNodes())
            $element->removeChild($element->firstChild);
        if (!empty($html)) {
            $fragment = $element->ownerDocument->createDocumentFragment();
            $fragment->appendXML($html);
            $element->appendChild($fragment);
        }
    }

    /**
     * @param \DOMElement $element
     * @return string
     */
    private function getOuterHTML(\DOMElement $element) {
        $doc = new \DOMDocument();
        $doc->appendChild($doc->importNode($element, true));

        return $doc->saveHTML();
    }

    public function appendHTML(\DOMNode $parent, $source) {
        $tmpDoc = new \DOMDocument();
        $tmpDoc->loadHTML('<?xml encoding="utf-8" ?>' . $source);
        $body = $tmpDoc->getElementsByTagName('body')->item(0);
        if ($body instanceof \DOMElement) {
            foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
                $node = $parent->ownerDocument->importNode($node, true);
                $parent->appendChild($node);
            }
        }
    }
}