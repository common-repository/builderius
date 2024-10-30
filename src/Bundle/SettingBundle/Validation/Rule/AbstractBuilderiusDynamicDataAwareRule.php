<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;

abstract class AbstractBuilderiusDynamicDataAwareRule extends AbstractRule
{
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        try {
            $dVal = $this->validateDynamic($input);
            if (true === $dVal) {
                return true;
            } else {
                return $this->validateStat($input);
            }
        } catch (\Exception $e) {
            return $this->validateStat($input);
        }
    }

    /**
     * @param mixed $input
     * @return bool
     */
    protected function validateDynamic($input)
    {
        if (!is_string($input)) {
            return false;
        }
        if (preg_match('/^\[\[(.*?)]]$/', $input)) {
            return true;
        } elseif (preg_match('/^\[\[\[(.*?)]]]$/', $input)) {
            return true;
        } elseif (preg_match('/^{{(.*?)}}$/', $input)) {
            return true;
        } elseif (preg_match('/^{{{(.*?)}}}$/', $input)) {
            return true;
        }

        return false;
    }

    protected function validateStat($input)
    {
        try {
            return $this->validateStatic($input);
        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $input
     * @return bool
     */
    abstract protected function validateStatic($input);
}