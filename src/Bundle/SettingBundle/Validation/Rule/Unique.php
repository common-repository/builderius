<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

class Unique extends AbstractRule
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @param string $field
     */
    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        if (!is_array($input)) {
            throw new \Exception('wrond input argument for validator unique');
        }
        $uniqueValues = [];
        foreach ($input as $inputItem) {
            if (in_array($inputItem[$this->field], $uniqueValues)) {
                return false;
            } else {
                $uniqueValues[] = $inputItem[$this->field];
            }
        }

        return true;
    }
}