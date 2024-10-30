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

use Builderius\Respect\Validation\Validatable;
/**
 * Abstract class to help on creating rules that wrap rules.
 *
 * @author Alasdair North <alasdair@runway.io>
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
abstract class AbstractWrapper extends \Builderius\Respect\Validation\Rules\AbstractRule
{
    /**
     * @var Validatable
     */
    private $validatable;
    /**
     * Initializes the rule.
     */
    public function __construct(\Builderius\Respect\Validation\Validatable $validatable)
    {
        $this->validatable = $validatable;
    }
    /**
     * {@inheritDoc}
     */
    public function assert($input) : void
    {
        $this->validatable->assert($input);
    }
    /**
     * {@inheritDoc}
     */
    public function check($input) : void
    {
        $this->validatable->check($input);
    }
    /**
     * {@inheritDoc}
     */
    public function validate($input) : bool
    {
        return $this->validatable->validate($input);
    }
    /**
     * {@inheritDoc}
     */
    public function setName(string $name) : \Builderius\Respect\Validation\Validatable
    {
        $this->validatable->setName($name);
        return parent::setName($name);
    }
}
