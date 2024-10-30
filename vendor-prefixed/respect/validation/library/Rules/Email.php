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

use Builderius\Egulias\EmailValidator\EmailValidator;
use Builderius\Egulias\EmailValidator\Validation\RFCValidation;
use function class_exists;
use function filter_var;
use function is_string;
use const FILTER_VALIDATE_EMAIL;
/**
 * Validates an email address.
 *
 * @author Andrey Kolyshkin <a.kolyshkin@semrush.com>
 * @author Eduardo Gulias Davis <me@egulias.com>
 * @author Henrique Moody <henriquemoody@gmail.com>
 * @author Paul Karikari <paulkarikari1@gmail.com>
 */
final class Email extends \Builderius\Respect\Validation\Rules\AbstractRule
{
    /**
     * @var EmailValidator|null
     */
    private $validator;
    /**
     * Initializes the rule assigning the EmailValidator instance.
     *
     * If the EmailValidator instance is not defined, tries to create one.
     */
    public function __construct(?\Builderius\Egulias\EmailValidator\EmailValidator $validator = null)
    {
        $this->validator = $validator ?: $this->createEmailValidator();
    }
    /**
     * {@inheritDoc}
     */
    public function validate($input) : bool
    {
        if (!\is_string($input)) {
            return \false;
        }
        if ($this->validator !== null) {
            return $this->validator->isValid($input, new \Builderius\Egulias\EmailValidator\Validation\RFCValidation());
        }
        return (bool) \filter_var($input, \FILTER_VALIDATE_EMAIL);
    }
    private function createEmailValidator() : ?\Builderius\Egulias\EmailValidator\EmailValidator
    {
        if (\class_exists(\Builderius\Egulias\EmailValidator\EmailValidator::class)) {
            return new \Builderius\Egulias\EmailValidator\EmailValidator();
        }
        return null;
    }
}
