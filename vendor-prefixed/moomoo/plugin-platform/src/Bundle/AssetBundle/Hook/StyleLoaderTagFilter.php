<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Formatter\HtmlAttributesFormatter;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;
class StyleLoaderTagFilter extends \Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $tag = \func_get_arg(0);
        $handle = \func_get_arg(1);
        if ($htmlAttributes = wp_styles()->get_data($handle, 'htmlAttributes')) {
            if (\is_array($htmlAttributes) && !empty($htmlAttributes)) {
                $formattedAttributes = [];
                foreach ($htmlAttributes as $key => $value) {
                    if (in_array($key, ['type', 'id'])) {
                        $formattedAttributes[] = \Builderius\MooMoo\Platform\Bundle\AssetBundle\Formatter\HtmlAttributesFormatter::format($key, $value);
                    }
                }
                if (!empty($formattedAttributes)) {
                    $formattedAttributes = \implode(' ', $formattedAttributes);
                    $tag = \preg_replace(':(?=>):', " {$formattedAttributes}", $tag, 1);
                }
                if (isset($htmlAttributes['type'])) {
                    $formattedType = \Builderius\MooMoo\Platform\Bundle\AssetBundle\Formatter\HtmlAttributesFormatter::format('type', $htmlAttributes['type']);
                    $tag = \preg_replace('/ type=\'text\/css\'/', " {$formattedType}", $tag, 1);
                }
                if (isset($htmlAttributes['id'])) {
                    $formattedId = \Builderius\MooMoo\Platform\Bundle\AssetBundle\Formatter\HtmlAttributesFormatter::format('id', $htmlAttributes['type']);
                    $tag = \preg_replace('/ id=\'[A-Za-z]+[\w\-:.]*\'/', " {$formattedId}", $tag, 1);
                }
            }
        }
        return $tag;
    }
}
