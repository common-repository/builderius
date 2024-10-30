<?php

namespace Builderius\Bundle\TemplateBundle\Sanitizer;

class CssContentSanitizer
{
    /**
     * @param string $css
     * @return string
     */
    public static function sanitize($css)
    {
        preg_match_all('/@media \(+[a-z0-9-: ]+\) {}/m', $css, $emptyMediaQueries);
        $emptyMediaQueries = array_unique($emptyMediaQueries[0]);

        foreach ($emptyMediaQueries as $emptyMediaQuery) {
            $css = str_replace($emptyMediaQuery, '', $css);
        }

        return trim($css);
    }
}