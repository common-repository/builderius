<?php

namespace Builderius\Bundle\ModuleBundle\EventListener;

use Builderius\Bundle\ModuleBundle\Helper\MustacheTemplateRenderingHelper;
use Builderius\Bundle\TemplateBundle\Event\HtmlContainingEvent;
use Builderius\Bundle\TemplateBundle\Helper\HtmlBeautifyHelper;

class DataContentModulesFinalHtmlRenderingEventListener
{
    const CHARACTERS_TO_DECODE = ["&quot;", "&amp;", "&lt;", "&gt;", "&#039;"];
    /**
     * @var MustacheTemplateRenderingHelper
     */
    private $mustacheTemplateRenderingHelper;

    /**
     * @param MustacheTemplateRenderingHelper $mustacheTemplateRenderingHelper
     */
    public function __construct(
        MustacheTemplateRenderingHelper $mustacheTemplateRenderingHelper
    ) {
        $this->mustacheTemplateRenderingHelper = $mustacheTemplateRenderingHelper;
    }

    /**
     * @param HtmlContainingEvent $event
     */
    public function onBuilderiusHtmlRendered(HtmlContainingEvent $event)
    {
        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
        libxml_use_internal_errors(true);

        $html = $event->getHtml();
        //return;
        preg_match_all('/&(?:[[:alpha:]][[:alnum:]]*|#(?:[[:digit:]]+|[Xx][[:xdigit:]]+));/', $html, $matches);
        $f = [];
        if (isset($matches[0]) && !empty($matches[0])) {
            $uniqueMatches = array_unique($matches[0]);
            foreach ($uniqueMatches as $k => $match) {
                $key = '-|' . $k . '|-';
                $f[$key] = $match;
                $html = str_replace($match, $key, $html);
            }
        }

        $doc = new \DOMDocument('1.0');
        $doc->formatOutput = true;

        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html);

        $xpath = new \DomXPath($doc);
        $dataContentElements = $xpath->query('//*[@data-content]');

        foreach ($dataContentElements as $dataContentElement) {
            $attr = $dataContentElement->attributes;
            $cont = $attr->getNamedItem('data-content');
            if (!empty($cont->nodeValue)) {
                $dataContent = $cont->nodeValue;
                foreach ($f as $k => $match) {
                    if (in_array($match, self::CHARACTERS_TO_DECODE)) {
                        $dataContent = str_replace($k, $match, $dataContent);
                    }
                }
                $dataContent = html_entity_decode($dataContent, ENT_QUOTES, 'UTF-8');
                $dataContent = json_decode($dataContent, true);
                if (is_array($dataContent)) {
                    $globals = null;
                    $glob = $attr->getNamedItem('data-globals');
                    if (!empty($glob->nodeValue)) {
                        $globals = $glob->nodeValue;
                        foreach ($f as $k => $match) {
                            if (in_array($match, self::CHARACTERS_TO_DECODE)) {
                                $globals = str_replace($k, $match, $globals);
                            }
                        }
                        $globals = html_entity_decode($globals, ENT_QUOTES, 'UTF-8');
                        $globals = json_decode($globals, true);
                    }
                    $originalTmpls = [];
                    $virtualTmpl = $doc->createElement('template');
                    $virtualTmpl->setAttribute('class', 'uniParentTmpl');
                    foreach ($dataContentElement->childNodes as $childNode) {
                        if (property_exists($childNode, 'tagName') && $childNode->tagName === 'template') {
                            $originalTmpls[] = $childNode;
                        }
                    }
                    if (!empty($originalTmpls)) {
                        foreach ($originalTmpls as $tmpl) {
                            $virtualTmpl->appendChild(clone $tmpl);
                        }

                        $decodedVirtualTmpl = $this->decodeDomElement($virtualTmpl, $f);
                        $rendered = $this->mustacheTemplateRenderingHelper->render($decodedVirtualTmpl, $dataContent, $globals);

                        $this->mustacheTemplateRenderingHelper->appendHTML($dataContentElement, $rendered);
                        $ssr = $attr->getNamedItem('data-ssr');
                        if ((bool)$ssr === true) {
                            foreach ($originalTmpls as $originalTmpl) {
                                $dataContentElement->removeChild($originalTmpl);
                            }
                            $dataContentElement->removeAttribute('data-content');
                            $dataContentElement->removeAttribute('data-globals');
                            $dataContentElement->removeAttribute('data-ssr');
                        }
                    }
                }
            }
        }
        $html = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', html_entity_decode($doc->saveHTML(), ENT_QUOTES, 'UTF-8'));
        $html = str_replace('<?xml encoding="utf-8" ?>', '', $html);
        /*$beautify = new HtmlBeautifyHelper();
        $html = $beautify->beautify($html);*/
        $html = str_replace(
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
                        str_replace(
                            "&amp;&amp;",
                            "&&",
                            $html
                        )
                    )
                )
            )
        );

        $xpath = new \DomXPath($doc);
        $elsWithDataContent = $xpath->query('//*[@data-content]');
        foreach ($elsWithDataContent as $dataEl) {
            $origAttr = $dataEl->getAttribute('data-content');
            $dataCont = esc_attr($dataEl->getAttribute('data-content'));
            $html = str_replace($origAttr, $dataCont, $html);
        }
        $elsWithDataGlobals = $xpath->query('//*[@data-globals]');
        foreach ($elsWithDataGlobals as $dataEl) {
            $origAttr = $dataEl->getAttribute('data-globals');
            $dataCont = esc_attr($dataEl->getAttribute('data-globals'));
            $html = str_replace($origAttr, $dataCont, $html);
        }
        foreach ($f as $k => $match) {
            $html = str_replace($k, $match, $html);
        }
        foreach ($f as $k => $match) {
            $html = str_replace(urlencode($k), $match, $html);
        }
        libxml_clear_errors();
        $html = str_replace(
            "&amp;&amp;",
            "&&",
            $html
        );
        preg_match_all('/\{\{(.*?)\}\}/s', $html, $localVars);
        foreach ($localVars[1] as $localVar) {
            //if (strpos($localVar, '%20')) {
                $html = str_replace(
                    sprintf("{{%s}}", $localVar),
                    sprintf("{{%s}}", urldecode($localVar)),
                    $html
                );
            //}
        }

        $event->setHtml($html . "\n");
    }

    /**
     * @param \DomNode $element
     * @param array $characters
     * @return \DomNode
     */
    private function decodeDomElement(\DomNode $element, array $characters)
    {
        if ($element->hasAttributes()) {
            $attr = $element->attributes;
            /** @var \DOMAttr $attribute */
            foreach ($attr as $attribute) {
                $attrName = $attribute->name;
                $attrValue = $attribute->value;
                foreach ($characters as $k => $match) {
                    if (in_array($match, self::CHARACTERS_TO_DECODE)) {
                        $attrValue = str_replace($k, $match, $attrValue);
                    }
                }
                foreach ($characters as $k => $match) {
                    if (in_array($match, self::CHARACTERS_TO_DECODE)) {
                        $attrValue = str_replace(urlencode($k), $match, $attrValue);
                    }
                }
                $attrValue = html_entity_decode($attrValue, ENT_QUOTES, 'UTF-8');
                $element->setAttribute($attrName, $attrValue);
            }
        }
        if ($element->hasChildNodes()) {
            foreach ($element->childNodes as $child) {
                $this->decodeDomElement($child, $characters);
            }
        }

        return $element;
    }
}