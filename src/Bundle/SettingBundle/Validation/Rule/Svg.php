<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Bundle\SettingBundle\Validation\Exta\SvgAllowedAttributes;
use Builderius\enshrined\svgSanitize\Sanitizer;
use Builderius\Respect\Validation\Rules\AbstractRule;

class Svg extends AbstractRule
{
    /**
     * {@inheritDoc}
     */
    public function validate($input) : bool
    {
        $formattedInput = trim(
            str_replace(
                '> <',
                '><',
                preg_replace(
                    '/\\s+/',
                    ' ',
                    str_replace(
                        '\t',
                        '',
                        str_replace(
                            '\n',
                            '',
                            $input
                        )
                    )
                )
            )
        );
        $sanitizer = new Sanitizer();
        $sanitizer->minify(true);
        $sanitizer->removeXMLTag(true);
        $sanitizer->setAllowedAttrs(new SvgAllowedAttributes());
        $sanitizedInput = trim($sanitizer->sanitize($input));

        $inputAttributes = $this->getSvgAttributes($input);
        ksort($inputAttributes);
        $sanitizedAttributes = $this->getSvgAttributes($sanitizedInput);
        ksort($sanitizedAttributes);

        $inputContent = $this->getSvgContent($formattedInput);
        $sanitizedContent = $this->getSvgContent($sanitizedInput);

        return $inputAttributes === $sanitizedAttributes && $inputContent === $sanitizedContent;
    }

    /**
     * @param string $input
     * @return array
     */
    private function getSvgAttributes($input)
    {
        $dom = new \DOMdocument();
        @$dom->loadHTML($input);
        $xpath = new \DOMXPath($dom);

        $attributes = [];
        $nodes = $xpath->query("//svg");
        if ($nodes) {
            $node = $nodes->item(0);
            if( $node->nodeType==XML_ELEMENT_NODE ) {
                foreach ($node->attributes as $attr) {
                    $attributes[$attr->nodeName] = $attr->nodeValue;
                }
            }
        }

        return $attributes;
    }

    /**
     * @param string $input
     * @return string
     */
    private function getSvgContent($input)
    {
        $dom = new \DOMdocument();
        @$dom->loadHTML($input);
        $xpath = new \DOMXPath($dom);
        $innerHtml = '';
        $nodes = $xpath->query("//svg");
        if ($nodes) {
            $node = $nodes->item(0);
            if( $node->nodeType==XML_ELEMENT_NODE ) {
                $innerHtml = $innerHtml . trim($node->nodeValue);
            }
        }
        $nodes = $xpath->query("//svg/*");
        if ($nodes) {
            foreach( $nodes as $node ) {
                $innerHtml = $innerHtml . $dom->saveHtml($node);
            }
        }

        return $innerHtml;
    }
}
