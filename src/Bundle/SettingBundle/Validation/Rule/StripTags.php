<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class StripTags extends AbstractRule
{
    /**
     * @var array|string
     */
    protected $allowedTags;

    /**
     * @param array|string $allowedTags
     */
    public function __construct($allowedTags)
    {
        $this->allowedTags = $allowedTags;
    }

    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        preg_match_all('/\[\[\[(.*?)\]\]\]/s', $input, $nonEscapedDataVars);
        $nonEscapedDataVarsNames = array_unique($nonEscapedDataVars[0]);
        if (!empty($nonEscapedDataVarsNames)) {
            foreach ($nonEscapedDataVarsNames as $nedvItem) {
                $input = str_replace($nedvItem, 'dataVar', $input);
            }
        }
        preg_match_all('/\[\[(.*?)\]\]/s', $input, $escapedDataVars);
        $escapedDataVarsNames = array_unique($escapedDataVars[0]);
        if (!empty($escapedDataVarsNames)) {
            foreach ($escapedDataVarsNames as $edvItem) {
                $input = str_replace($edvItem, 'dataVar', $input);
            }
        }
        preg_match_all('/{{{(.*?)}}}/s', $input, $nonEscapedDataVars);
        $nonEscapedDataVarsNames = array_unique($nonEscapedDataVars[0]);
        if (!empty($nonEscapedDataVarsNames)) {
            foreach ($nonEscapedDataVarsNames as $nedvItem) {
                $input = str_replace($nedvItem, 'dataVar', $input);
            }
        }
        preg_match_all('/{{(.*?)}}/s', $input, $escapedDataVars);
        $escapedDataVarsNames = array_unique($escapedDataVars[0]);
        if (!empty($escapedDataVarsNames)) {
            foreach ($escapedDataVarsNames as $edvItem) {
                $input = str_replace($edvItem, 'dataVar', $input);
            }
        }
        preg_match_all('/<[^a-zA-Z\/]/im', $input, $lt);
        if (!empty($lt)) {
            foreach ($lt[0] as $ltItem) {
                $input = str_replace($ltItem, '&lt;', $input);
            }
        }
        preg_match_all('/[^a-zA-Z\/\'\"]>/im', $input, $gt);
        if (!empty($gt)) {
            foreach ($gt[0] as $gtItem) {
                $input = str_replace($gtItem, '&gt;', $input);
            }
        }

        return $input === strip_tags($input, $this->allowedTags);
    }
}