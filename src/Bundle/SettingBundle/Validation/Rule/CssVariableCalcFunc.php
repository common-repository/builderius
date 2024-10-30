<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

class CssVariableCalcFunc extends AbstractBuilderiusDynamicDataAwareRule
{
    /**
     * @inheritDoc
     */
    protected function validateStatic($input): bool
    {
        if ($input['a1'] === 'any-value' && $input['a3'] === 'calc' && $input['a2'] !== null && $input['b2'] !== null) {
            if (
                preg_match(
                    '/calc\(( )?((?R)|(?P<clamp>(clamp\(((?R)|(?&css_var)|(?&min_max)|(?&attr)|(?&number_with_unit))+(?:,( )?((?R)|(?&css_var)|(?&min_max)|(?&attr)|(?&number_with_unit))+){2}\)))|(?P<min_max>((min|max)\(((?R)|(?&css_var)|(?&clamp)|(?&attr)|(?&number_with_unit))+(?:,( )?((?R)|(?&css_var)|(?&clamp)|(?&attr)|(?&number_with_unit))+)*\)))|(?P<attr>(attr\([a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*\)))|(?P<css_var>(var\(--[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*\)))|(?P<number_with_unit>([\d\.]+(%|vh|vw|vmin|vmax|em|rem|px|cm|ex|in|mm|pc|pt|ch|q|deg|rad|grad|turn|s|ms|hz|khz)?))){1}((( )[+\-\*\/]( )){1}((?R)|(?&clamp)|(?&min_max)|(?&attr)|(?&css_var)|(?&number_with_unit)))*( )?\)/',
                    sprintf('calc(%s)', $input['b2'])
                )
            ) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }
}