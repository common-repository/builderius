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

use Builderius\Respect\Validation\Exceptions\NestedValidationException;
use Builderius\Respect\Validation\Exceptions\ValidationException;
use Builderius\Respect\Validation\Validatable;
use function array_filter;
use function array_map;
/**
 * Abstract class for rules that are composed by other rules.
 *
 * @author Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 * @author Henrique Moody <henriquemoody@gmail.com>
 * @author Wojciech Frącz <fraczwojciech@gmail.com>
 */
abstract class AbstractComposite extends \Builderius\Respect\Validation\Rules\AbstractRule
{
    /**
     * @var Validatable[]
     */
    private $rules = [];
    /**
     * Initializes the rule adding other rules to the stack.
     */
    public function __construct(\Builderius\Respect\Validation\Validatable ...$rules)
    {
        $this->rules = $rules;
    }
    /**
     * {@inheritDoc}
     */
    public function setName(string $name) : \Builderius\Respect\Validation\Validatable
    {
        $parentName = $this->getName();
        foreach ($this->rules as $rule) {
            $ruleName = $rule->getName();
            if ($ruleName && $parentName !== $ruleName) {
                continue;
            }
            $rule->setName($name);
        }
        return parent::setName($name);
    }
    /**
     * Append a rule into the stack of rules.
     *
     * @return AbstractComposite
     */
    public function addRule(\Builderius\Respect\Validation\Validatable $rule) : self
    {
        if ($this->shouldHaveNameOverwritten($rule) && $this->getName() !== null) {
            $rule->setName($this->getName());
        }
        $this->rules[] = $rule;
        return $this;
    }
    /**
     * Returns all the rules in the stack.
     *
     * @return Validatable[]
     */
    public function getRules() : array
    {
        return $this->rules;
    }
    /**
     * Returns all the exceptions throw when asserting all rules.
     *
     * @param mixed $input
     *
     * @return ValidationException[]
     */
    protected function getAllThrownExceptions($input) : array
    {
        return \array_filter(\array_map(function (\Builderius\Respect\Validation\Validatable $rule) use($input) : ?ValidationException {
            try {
                $rule->assert($input);
            } catch (\Builderius\Respect\Validation\Exceptions\ValidationException $exception) {
                $this->updateExceptionTemplate($exception);
                return $exception;
            }
            return null;
        }, $this->getRules()));
    }
    private function shouldHaveNameOverwritten(\Builderius\Respect\Validation\Validatable $rule) : bool
    {
        return $this->hasName($this) && !$this->hasName($rule);
    }
    private function hasName(\Builderius\Respect\Validation\Validatable $rule) : bool
    {
        return $rule->getName() !== null;
    }
    private function updateExceptionTemplate(\Builderius\Respect\Validation\Exceptions\ValidationException $exception) : void
    {
        if ($this->template === null || $exception->hasCustomTemplate()) {
            return;
        }
        $exception->updateTemplate($this->template);
        if (!$exception instanceof \Builderius\Respect\Validation\Exceptions\NestedValidationException) {
            return;
        }
        foreach ($exception->getChildren() as $childException) {
            $this->updateExceptionTemplate($childException);
        }
    }
}
