<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

class TagId extends AbstractBuilderiusDynamicDataAwareRule
{
    /**
     * @inheritDoc
     */
    protected function validateStatic($input): bool
    {
        if (preg_match('/^[A-Za-z]+[\w\-:.]*$/', $input)) {
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