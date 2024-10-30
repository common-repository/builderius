<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class Color extends AbstractRule
{
    private $specialValues = ['currentcolor', 'transparent', 'inherit', 'initial', 'unset'];

    /**
     * @param array $specialValues
     */
    public function __construct(array $specialValues = [])
    {
        if (!empty($specialValues)) {
            $this->specialValues = $specialValues;
        }
    }

    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        $rgbaRegex = '/^rgba[(](?:\s*0*(?:\d\d?(?:\.\d+)?(?:\s*%)?|\.\d+\s*%|100(?:\.0*)?\s*%|(?:1\d\d|2[0-4]\d|25[0-5])(?:\.\d+)?)\s*,){3}\s*(0|0(?:\.\d+)|1(?:\.0+)?)\s*[)]$/';
        $rgbRegex = '/^rgb[(](?:\s*0*(?:\d\d?(?:\.\d+)?(?:\s*%)?|\.\d+\s*%|100(?:\.0*)?\s*%|(?:1\d\d|2[0-4]\d|25[0-5])(?:\.\d+)?)\s*(?:,(?![)])|(?=[)]))){3}[)]$/';
        $hslaRegex = '/^hsla\(\s*(-?\d+|-?\d*.\d+)\s*,\s*(-?\d+|-?\d*.\d+)%\s*,\s*(-?\d+|-?\d*.\d+)%\s*,\s*(-?\d+|-?\d*.\d+)\s*\)$/';
        $hslRegex = '/^hsl\(\s*(\d+)\s*,\s*(\d+(?:\.\d+)?%)\s*,\s*(\d+(?:\.\d+)?%)\)$/';
        $hexRegex = '/^#[a-f\d]{3}(?:[a-f\d]?|(?:[a-f\d]{3}(?:[a-f\d]{2})?)?)\b/';
        if (preg_match($rgbaRegex, $input) || preg_match($rgbRegex, $input) || preg_match($hslaRegex, $input) || preg_match($hslRegex, $input) || preg_match($hexRegex, $input) || in_array($input, $this->specialValues)) {
            return true;
        }

        return false;
    }
}