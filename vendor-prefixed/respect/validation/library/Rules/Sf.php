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

use Builderius\Respect\Validation\Exceptions\SfException;
use Builderius\Respect\Validation\Exceptions\ValidationException;
use Builderius\Symfony\Component\Validator\Constraint;
use Builderius\Symfony\Component\Validator\ConstraintViolationList;
use Builderius\Symfony\Component\Validator\Validation;
use Builderius\Symfony\Component\Validator\Validator\ValidatorInterface;
use function trim;
/**
 * Validate the input with a Symfony Validator (>=4.0 or >=3.0) Constraint.
 *
 * @author Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 * @author Augusto Pascutti <augusto@phpsp.org.br>
 * @author Henrique Moody <henriquemoody@gmail.com>
 * @author Hugo Hamon <hugo.hamon@sensiolabs.com>
 */
final class Sf extends \Builderius\Respect\Validation\Rules\AbstractRule
{
    /**
     * @var Constraint
     */
    private $constraint;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * Initializes the rule with the Constraint and the Validator.
     *
     * In the the Validator is not defined, tries to create one.
     */
    public function __construct(\Builderius\Symfony\Component\Validator\Constraint $constraint, ?\Builderius\Symfony\Component\Validator\Validator\ValidatorInterface $validator = null)
    {
        $this->constraint = $constraint;
        $this->validator = $validator ?: \Builderius\Symfony\Component\Validator\Validation::createValidator();
    }
    /**
     * {@inheritDoc}
     */
    public function assert($input) : void
    {
        /** @var ConstraintViolationList $violations */
        $violations = $this->validator->validate($input, $this->constraint);
        if ($violations->count() === 0) {
            return;
        }
        if ($violations->count() === 1) {
            throw $this->reportError($input, ['violations' => $violations[0]->getMessage()]);
        }
        throw $this->reportError($input, ['violations' => \trim($violations->__toString())]);
    }
    /**
     * {@inheritDoc}
     */
    public function reportError($input, array $extraParams = []) : \Builderius\Respect\Validation\Exceptions\ValidationException
    {
        $exception = parent::reportError($input, $extraParams);
        if (isset($extraParams['violations'])) {
            $exception->updateTemplate($extraParams['violations']);
        }
        return $exception;
    }
    /**
     * {@inheritDoc}
     */
    public function validate($input) : bool
    {
        try {
            $this->assert($input);
        } catch (\Builderius\Respect\Validation\Exceptions\SfException $exception) {
            return \false;
        }
        return \true;
    }
}
