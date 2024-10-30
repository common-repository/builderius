<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

class CssVariableAnyValue extends AbstractBuilderiusDynamicDataAwareRule
{
    /**
     * @param mixed $input
     * @return bool
     */
    protected function validateDynamic($input)
    {
        if ($input['a1'] === 'any-value' && $input['b2'] !== null) {
            return parent::validateDynamic($input['b2']);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function validateStatic($input): bool
    {
        if ($input['a1'] === 'any-value' && $input['b2'] !== null) {
            $open = substr_count($input['b2'], '(');
            $closed = substr_count($input['b2'], ')');
            if ($open !== $closed) {
                return false;
            }
            $val = $input['b2'];
            preg_match_all(
                '/\[\[\[(?:[a-zA-Z_^]+[\w^]*(\[([0-9])])*)(?:\.[a-zA-Z_^]+[\w^]*(\[([0-9])])*)*]]]/',
                $val,
                $neskDataVariables
            );
            if (!empty($neskDataVariables)) {
                $valid = true;
                foreach ($neskDataVariables as $variable) {
                    if (is_array($variable) && isset($variable[0])) {
                        $variable = $variable[0];
                    }
                    if ($variable === "" || is_array($variable)) {
                        continue;
                    }
                    if (!parent::validateDynamic($variable)) {
                        $valid = false;
                        break;
                    }
                    $val = str_replace($variable, 'variable', $val);
                }
                if (!$valid) {
                    return false;
                }
            }
            preg_match_all(
                '/\[\[(?:[a-zA-Z_^]+[\w^]*(\[([0-9])])*)(?:\.[a-zA-Z_^]+[\w^]*(\[([0-9])])*)*]]/',
                $val,
                $eskDataVariables
            );
            if (!empty($eskDataVariables)) {
                $valid = true;
                foreach ($eskDataVariables as $variable) {
                    if (is_array($variable) && isset($variable[0])) {
                        $variable = $variable[0];
                    }
                    if ($variable === "" || is_array($variable)) {
                        continue;
                    }
                    if (!parent::validateDynamic($variable)) {
                        $valid = false;
                        break;
                    }
                    $val = str_replace($variable, 'variable', $val);
                }
                if (!$valid) {
                    return false;
                }
            }
            if (
                preg_match(
                    '/^[a-zA-Z0-9\s+\-*\/%().:,"_#]*$/',
                    $val
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