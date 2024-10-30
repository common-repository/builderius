<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class MediaQuery extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        $regexArray = ['/^@media\s+(\s*(only|not)?\s*(all|print|screen|speech)?\s*(\(\s*(',
                    '-\w+-(min-|max-)?([\w\-]+)\s*(:?\s*[0-9a-z.\/]+)?|',
                    '(any-hover|hover):\s*(:?\s*(none|hover)+)+|',
                    '(any-pointer|pointer):\s*(:?\s*(none|coarse|fine)+)+|',
                    '(aspect-ratio|min-aspect-ratio|max-aspect-ratio):\s*(:?\s*(\d+\/\d+)+)+|',
                    '(color|min-color|max-color|color-index|min-color-index|max-color-index|monochrome|min-monochrome|max-monochrome):\s*(:?\s*\d+)+|',
                    'color-gamut:\s*(:?\s*(srgb|p3|rec2020)+)+|',
                    'display-mode:\s*(:?\s*(fullscreen|standalone|minimal-ui|browser)+)+|',
                    'forced-colors:\s*(:?\s*(none|active)+)+|',
                    'inverted-colors:\s*(:?\s*(none|inverted)+)+|',
                    'grid:\s*(:?\s*(0|1)+)+|',
                    'monochrome|',
                    'orientation:\s*(:?\s*(portrait|landscape)+)+|',
                    'overflow-block:\s*(:?\s*(none|scroll|optional-paged|paged)+)+|',
                    'overflow-inline:\s*(:?\s*(none|scroll)+)+|',
                    'prefers-color-scheme:\s*(:?\s*(light|dark)+)+|',
                    'prefers-contrast:\s*(:?\s*(no-preference|more|less)+)+|',
                    'prefers-reduced-motion:\s*(:?\s*(no-preference|reduce)+)+|',
                    'prefers-reduced-transparency:\s*(:?\s*(no-preference|reduce)+)+|',
                    '(resolution|min-resolution|max-resolution):\s*(:?\s*(\d+(\.\d+)?(dpi|dpcm|dppx)+))+|',
                    'scan:\s*(:?\s*(interlace|progressive)+)+|',
                    'scripting:\s*(:?\s*(none|initial-only|enabled)+)+|',
                    'update:\s*(:?\s*(none|slow|fast)+)+|',
                    '(width|min-width|max-width|height|min-height|max-height):\s*(:?\s*(\d+(\.\d+)?(em|ex|ch|rem|vh|vw|vmin|vmax|px|mm|cm|in|pt|pc|mozmm)+))+',
                ')\s*\))?\s*(,|and)?\s*)+$/'
        ];
        $regex = implode('', $regexArray);
        if (preg_match($regex, $input) && trim($input) !== '@media' && substr(trim($input), -1) !== ',' && substr(trim($input), -3) !== 'and' &&
            strpos(str_replace(' ', '', $input), 'andand') === false && strpos(str_replace(' ', '', $input), ',,') === false && strpos(str_replace(' ', '', $input), 'and,') === false && strpos(str_replace(' ', '', $input), ',and') === false) {
            return true;
        }

        return false;
    }
}