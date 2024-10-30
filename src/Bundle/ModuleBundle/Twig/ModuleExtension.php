<?php

namespace Builderius\Bundle\ModuleBundle\Twig;

use Builderius\enshrined\svgSanitize\Sanitizer;
use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\TwigFunction;

class ModuleExtension extends AbstractExtension
{
    const NAME = 'builderius_module';

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'builderius_format_htmlattributes',
                [$this, 'formatHtmlAttributes']
            ),
            new TwigFunction(
                'builderius_format_datetime',
                [$this, 'formatDateTime']
            ),
            new TwigFunction(
                'builderius_get_svg_attributes',
                [$this, 'getSvgAttributes']
            ),
            new TwigFunction(
                'builderius_get_svg_content',
                [$this, 'getSvgContent']
            ),
            new TwigFunction(
                'builderius_render_hidden_form_fields',
                [$this, 'renderHiddenFormFields']
            ),
            new TwigFunction(
                'builderius_get_form_field_names',
                [$this, 'getFormFieldNames']
            ),
            new TwigFunction(
                'builderius_filter_cookie_notice_htmlattributes',
                [$this, 'filterCookieNoticeHtmlAttributes']
            )
        ];
    }

    /**
     * @param $value
     * @return string
     */
    public function formatHtmlAttributes($value)
    {
        $result = '';
        foreach ($value as $item) {
            if (strpos($item['name'], 'data-expression') === 0 && strpos($item['value'], '[[') === 0) {
                if (preg_match('/^\[\[\[(.*?)]]]$/', $item['value'])) {
                    $result .= "[^((builderius_data_var(\"" . str_replace(']]]', '', str_replace('[[[', '', esc_attr($item['value']))) . "\")|raw))|raw^] ";
                } elseif (preg_match('/^\[\[(.*?)]]$/', $item['value'])) {
                    $result .= "[^((builderius_data_var_escaped(\"" . str_replace(']]', '', str_replace('[[', '', esc_attr($item['value']))) . "\")|raw))|raw^] ";
                }
            } else {
                if (null === $item['value']) {
                    $result .= $item['name'] . " ";
                }else {
                    if (preg_match('/^\[\[\[(.*?)]]]$/', $item['value'])) {
                        $result .= "[^('" . $item['name'] . "=\"' ~ (builderius_data_var('" . str_replace(']]]', '', str_replace('[[[', '', esc_attr($item['value']))) . "')|raw)|esc_attr ~ '\"')|raw^] ";
                    } elseif (preg_match('/^\[\[(.*?)]]$/', $item['value'])) {
                        $result .= "[^('" . $item['name'] . "=\"' ~ (builderius_data_var_escaped('" . str_replace(']]', '', str_replace('[[', '', esc_attr($item['value']))) . "')|raw)|esc_attr ~ '\"')|raw^] ";
                    } else {
                        $result .= $item['name'] . "=\"" . esc_attr($item['value']) . "\" ";
                    }
                }
            }
        }

        return trim($result);
    }

    /**
     * @param $value
     * @return string
     */
    public function filterCookieNoticeHtmlAttributes($value)
    {
        foreach ($value as $i => $item) {
            if ($item['name'] === 'open') {
                unset($value[$i]);
            }
        }
        ksort($value);

        return $value;
    }

    /**
     * @param string $datetimeString
     * @param string|null $format
     * @return string
     * @throws \Exception
     */
    public function formatDateTime($datetimeString, $format = null)
    {
        if (!$datetimeString || !$format || preg_match('/{{(.*?)}}/', $datetimeString) || preg_match('/{{{(.*?)}}}/', $datetimeString)) {
            return $datetimeString;
        }
        if (preg_match('/^\[\[\[(.*?)\]\]\]$/', $datetimeString)) {
            return sprintf(
                "[^builderius_format_datetime(builderius_data_var('%s')|raw, \"%s\")^]",
                str_replace(']]]', '', str_replace('[[[', '', $datetimeString)),
                $format
            );
        } elseif (preg_match('/^\[\[(.*?)\]\]$/', $datetimeString)) {
            return sprintf(
                "[^builderius_format_datetime(builderius_data_var_escaped('%s')|raw, \"%s\")^]",
                str_replace(']]', '', str_replace('[[', '', $datetimeString)),
                $format
            );
        }
        try {
            $utcTimeZone = new \DateTimeZone('UTC');
            $datetime = new \DateTime($datetimeString, $utcTimeZone);

            return $datetime->format($format);
        } catch (\Exception $e) {
            return '';
        }

    }

    /**
     * @param string $input
     * @return string
     */
    public function getSvgAttributes($input)
    {
        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

        $input = $this->sanitizeSvg($input);
        $dom = new \DOMdocument();
        @$dom->loadHTML($input);
        $xpath = new \DOMXPath($dom);

        $attributes = '';
        $nodes = $xpath->query("//svg");
        if ($nodes) {
            $node = $nodes->item(0);
            if( $node->nodeType==XML_ELEMENT_NODE ) {
                foreach ($node->attributes as $attr) {
                    $attributes = $attributes . ' ' . $attr->nodeName . '="' . $attr->nodeValue . '"';
                }
            }
        }

        return $attributes;
    }

    /**
     * @param string $input
     * @return string
     */
    public function getSvgContent($input)
    {
        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

        $input = $this->sanitizeSvg($input);
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

    /**
     * @param string $input
     * @return string
     */
    protected function sanitizeSvg($input)
    {
        $sanitizer = new Sanitizer();
        $sanitizer->minify(true);
        $sanitizer->removeXMLTag(true);

        return trim($sanitizer->sanitize($input));
    }

    /**
     * @param array $fieldNames
     * @return string
     */
    public function renderHiddenFormFields(array $fieldNames)
    {
        $result = '';
        foreach ($_GET as $key => $value) {
            if (!in_array($key, $fieldNames)) {
                $result .= sprintf('<input type="hidden" name="%s" value="%s" />', $key, $value);
            }
        }

        return $result;
    }

    /**
     * @param array $fields
     * @return string
     */
    public function getFormFieldNames(array $fields)
    {
        $names = [];
        foreach ($fields as $field) {
            preg_match('/name="(.*?)"/', $field, $nm);
            if (isset($nm[1]) && strlen($nm[1]) > 0) {
                $names[] = $nm[1];
            }
        }

        return !empty($names) ? sprintf('["%s"]', implode('","', $names)) : '[]';
    }
}