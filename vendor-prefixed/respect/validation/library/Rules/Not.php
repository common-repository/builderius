<?php

/*
 * This file is part of Respect/Validation.
 *
 * (c) Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */
declare (strict_types=1);
namespace Builderius\Respect\Validation\Rules;

use Builderius\Respect\Validation\Exceptions\ValidationException;
use Builderius\Respect\Validation\Validatable;
use function array_shift;
use function count;
use function current;
/**
 * @author Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 * @author Caio César Tavares <caiotava@gmail.com>
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
final class Not extends \Builderius\Respect\Validation\Rules\AbstractRule
{
    /**
     * @var Validatable
     */
    private $rule;
    public function __construct(\Builderius\Respect\Validation\Validatable $rule)
    {
        $this->rule = $this->extractNegatedRule($rule);
    }
    public function getNegatedRule() : \Builderius\Respect\Validation\Validatable
    {
        return $this->rule;
    }
    public function setName(string $name) : \Builderius\Respect\Validation\Validatable
    {
        $this->rule->setName($name);
        return parent::setName($name);
    }
    /**
     * {@inheritDoc}
     */
    public function validate($input) : bool
    {
        return $this->rule->validate($input) === \false;
    }
    /**
     * {@inheritDoc}
     */
    public function assert($input) : void
    {
        if ($this->validate($input)) {
            return;
        }
        $rule = $this->rule;
        if ($rule instanceof \Builderius\Respect\Validation\Rules\AllOf) {
            $rule = $this->absorbAllOf($rule, $input);
        }
        $exception = $rule->reportError($input);
        $exception->updateMode(\Builderius\Respect\Validation\Exceptions\ValidationException::MODE_NEGATIVE);
        throw $exception;
    }
    /**
     * @param mixed $input
     */
    private function absorbAllOf(\Builderius\Respect\Validation\Rules\AllOf $rule, $input) : \Builderius\Respect\Validation\Validatable
    {
        $rules = $rule->getRules();
        while ($current = \array_shift($rules)) {
            $rule = $current;
            if (!$rule instanceof \Builderius\Respect\Validation\Rules\AllOf) {
                continue;
            }
            if (!$rule->validate($input)) {
                continue;
            }
            $rules = $rule->getRules();
        }
        return $rule;
    }
    private function extractNegatedRule(\Builderius\Respect\Validation\Validatable $rule) : \Builderius\Respect\Validation\Validatable
    {
        if ($rule instanceof self && $rule->getNegatedRule() instanceof self) {
            return $this->extractNegatedRule($rule->getNegatedRule()->getNegatedRule());
        }
        if (!$rule instanceof \Builderius\Respect\Validation\Rules\AllOf) {
            return $rule;
        }
        $rules = $rule->getRules();
        if (\count($rules) === 1) {
            return $this->extractNegatedRule(\current($rules));
        }
        return $rule;
    }
}
