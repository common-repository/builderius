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

use Builderius\Respect\Validation\Exceptions\ComponentException;
use Builderius\Respect\Validation\Validatable;
use function array_key_exists;
use function array_map;
use function count;
use function current;
use function is_array;
/**
 * Validates a keys in a defined structure.
 *
 * @author Emmerson Siqueira <emmersonsiqueira@gmail.com>
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
final class KeySet extends \Builderius\Respect\Validation\Rules\AbstractWrapper
{
    /**
     * @var mixed[]
     */
    private $keys;
    /**
     * @var Key[]
     */
    private $keyRules;
    /**
     * Initializes the rule.
     *
     * @param Validatable[] ...$validatables
     */
    public function __construct(\Builderius\Respect\Validation\Validatable ...$validatables)
    {
        $this->keyRules = \array_map([$this, 'getKeyRule'], $validatables);
        $this->keys = \array_map([$this, 'getKeyReference'], $this->keyRules);
        parent::__construct(new \Builderius\Respect\Validation\Rules\AllOf(...$this->keyRules));
    }
    /**
     * {@inheritDoc}
     */
    public function assert($input) : void
    {
        if (!$this->hasValidStructure($input)) {
            throw $this->reportError($input);
        }
        parent::assert($input);
    }
    /**
     * {@inheritDoc}
     */
    public function check($input) : void
    {
        if (!$this->hasValidStructure($input)) {
            throw $this->reportError($input);
        }
        parent::check($input);
    }
    /**
     * {@inheritDoc}
     */
    public function validate($input) : bool
    {
        if (!$this->hasValidStructure($input)) {
            return \false;
        }
        return parent::validate($input);
    }
    /**
     * @throws ComponentException
     */
    private function getKeyRule(\Builderius\Respect\Validation\Validatable $validatable) : \Builderius\Respect\Validation\Rules\Key
    {
        if ($validatable instanceof \Builderius\Respect\Validation\Rules\Key) {
            return $validatable;
        }
        if (!$validatable instanceof \Builderius\Respect\Validation\Rules\AllOf || \count($validatable->getRules()) !== 1) {
            throw new \Builderius\Respect\Validation\Exceptions\ComponentException('KeySet rule accepts only Key rules');
        }
        return $this->getKeyRule(\current($validatable->getRules()));
    }
    /**
     * @return mixed
     */
    private function getKeyReference(\Builderius\Respect\Validation\Rules\Key $rule)
    {
        return $rule->getReference();
    }
    /**
     * @param mixed $input
     */
    private function hasValidStructure($input) : bool
    {
        if (!\is_array($input)) {
            return \false;
        }
        foreach ($this->keyRules as $keyRule) {
            if (!\array_key_exists($keyRule->getReference(), $input) && $keyRule->isMandatory()) {
                return \false;
            }
            unset($input[$keyRule->getReference()]);
        }
        return \count($input) == 0;
    }
}