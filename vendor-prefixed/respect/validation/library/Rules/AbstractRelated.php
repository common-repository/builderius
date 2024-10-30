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
use function is_scalar;
/**
 * @author Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 * @author Emmerson Siqueira <emmersonsiqueira@gmail.com>
 * @author Henrique Moody <henriquemoody@gmail.com>
 * @author Nick Lombard <github@jigsoft.co.za>
 */
abstract class AbstractRelated extends \Builderius\Respect\Validation\Rules\AbstractRule
{
    /**
     * @var bool
     */
    private $mandatory = \true;
    /**
     * @var mixed
     */
    private $reference;
    /**
     * @var Validatable|null
     */
    private $rule;
    /**
     * @param mixed $input
     */
    public abstract function hasReference($input) : bool;
    /**
     * @param mixed $input
     *
     * @return mixed
     */
    public abstract function getReferenceValue($input);
    /**
     * @param mixed $reference
     */
    public function __construct($reference, ?\Builderius\Respect\Validation\Validatable $rule = null, bool $mandatory = \true)
    {
        $this->reference = $reference;
        $this->rule = $rule;
        $this->mandatory = $mandatory;
        if ($rule && $rule->getName() !== null) {
            $this->setName($rule->getName());
        } elseif (\is_scalar($reference)) {
            $this->setName((string) $reference);
        }
    }
    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }
    public function isMandatory() : bool
    {
        return $this->mandatory;
    }
    /**
     * {@inheritDoc}
     */
    public function setName(string $name) : \Builderius\Respect\Validation\Validatable
    {
        parent::setName($name);
        if ($this->rule instanceof \Builderius\Respect\Validation\Validatable) {
            $this->rule->setName($name);
        }
        return $this;
    }
    /**
     * {@inheritDoc}
     */
    public function assert($input) : void
    {
        $hasReference = $this->hasReference($input);
        if ($this->mandatory && !$hasReference) {
            throw $this->reportError($input, ['hasReference' => \false]);
        }
        if ($this->rule === null || !$hasReference) {
            return;
        }
        try {
            $this->rule->assert($this->getReferenceValue($input));
        } catch (\Builderius\Respect\Validation\Exceptions\ValidationException $validationException) {
            /** @var NestedValidationException $nestedValidationException */
            $nestedValidationException = $this->reportError($this->reference, ['hasReference' => \true]);
            $nestedValidationException->addChild($validationException);
            throw $nestedValidationException;
        }
    }
    /**
     * {@inheritDoc}
     */
    public function check($input) : void
    {
        $hasReference = $this->hasReference($input);
        if ($this->mandatory && !$hasReference) {
            throw $this->reportError($input, ['hasReference' => \false]);
        }
        if ($this->rule === null || !$hasReference) {
            return;
        }
        $this->rule->check($this->getReferenceValue($input));
    }
    /**
     * {@inheritDoc}
     */
    public function validate($input) : bool
    {
        $hasReference = $this->hasReference($input);
        if ($this->mandatory && !$hasReference) {
            return \false;
        }
        if ($this->rule === null || !$hasReference) {
            return \true;
        }
        return $this->rule->validate($this->getReferenceValue($input));
    }
}
