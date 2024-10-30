<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

class AttributeValue extends AbstractBuilderiusDynamicDataAwareRule
{
    /**
     * @inheritDoc
     */
    protected function validateStatic($input): bool
    {
        if (strpos($input, '{') !== false) {
            json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return true;
            } else {
                return $this->checkOpenClosedPairs('{', '}', $input);
            }
        } elseif (preg_match('/^[a-zA-Z0-9\$£€~_|:;., }{\][\/()*?!^@\-+=\'"#&$%\x00-\xFF]*$/', $input)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $openSymbol
     * @param string $closeSymbol
     * @param string $input
     * @return bool
     */
    private function checkOpenClosedPairs($openSymbol, $closeSymbol, $input)
    {
        $countOpen = substr_count($input,$openSymbol);
        $countClose = substr_count($input,$closeSymbol);
        if ($countOpen === $countClose) {
            return true;
        }

        return false;
    }
}