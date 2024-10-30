<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class DataVariableJsonValue extends AbstractRule
{
    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        if ($input['a1'] === 'json') {
            if ($input['c1'] === null) {
                return false;
            } else {
                if (is_string($input['c1'])) {
                    json_decode($input['c1'], true);
                } else {
                    json_decode(json_encode($input['c1'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE), true);
                }
                if (json_last_error() === JSON_ERROR_NONE) {
                    return true;
                } else {
                    return false;
                }
            }
        } elseif($input['a1'] === 'graphQLQuery') {
            if (!isset($input['d1']) || $input['d1'] === null) {
                return true;
            } else {
                if (is_string($input['d1'])) {
                    json_decode($input['d1'], true);
                } else {
                    json_decode(json_encode($input['d1'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE), true);
                }
                if (json_last_error() === JSON_ERROR_NONE) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        return true;
    }
}