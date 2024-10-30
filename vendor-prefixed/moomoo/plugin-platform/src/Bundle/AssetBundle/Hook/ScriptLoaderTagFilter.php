<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Hook;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Formatter\HtmlAttributesFormatter;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;
class ScriptLoaderTagFilter extends \Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter
{
    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $tag = \func_get_arg(0);
        $handle = \func_get_arg(1);
        if ($htmlAttributes = wp_scripts()->get_data($handle, 'htmlAttributes')) {
            if (\is_array($htmlAttributes) && !empty($htmlAttributes)) {
                $formattedAttributes = [];
                foreach ($htmlAttributes as $key => $value) {
                    if (in_array($key, ['type', 'id'])) {
                        $formattedAttributes[] = \Builderius\MooMoo\Platform\Bundle\AssetBundle\Formatter\HtmlAttributesFormatter::format($key, $value);
                    }
                }
                if (!empty($formattedAttributes)) {
                    $formattedAttributes = \implode(' ', $formattedAttributes);
                    $tag = \preg_replace(':(?=></script>):', " {$formattedAttributes}", $tag, 1);
                }
                if (isset($htmlAttributes['type'])) {
                    $formattedType = \Builderius\MooMoo\Platform\Bundle\AssetBundle\Formatter\HtmlAttributesFormatter::format('type', $htmlAttributes['type']);
                    $tag = \preg_replace('/ type=\'text\/javascript\'/', " {$formattedType}", $tag, 1);
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
