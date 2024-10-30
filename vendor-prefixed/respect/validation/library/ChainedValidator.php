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
namespace Builderius\Respect\Validation;

use finfo;
use Builderius\Respect\Validation\Rules\Key;
use Builderius\Symfony\Component\Validator\Constraint;
use Builderius\Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidator;
use Builderius\Zend\Validator\ValidatorInterface as ZendValidator;
interface ChainedValidator extends \Builderius\Respect\Validation\Validatable
{
    public function allOf(\Builderius\Respect\Validation\Validatable ...$rule) : \Builderius\Respect\Validation\ChainedValidator;
    public function alnum(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public function alpha(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public function alwaysInvalid() : \Builderius\Respect\Validation\ChainedValidator;
    public function alwaysValid() : \Builderius\Respect\Validation\ChainedValidator;
    public function anyOf(\Builderius\Respect\Validation\Validatable ...$rule) : \Builderius\Respect\Validation\ChainedValidator;
    public function arrayType() : \Builderius\Respect\Validation\ChainedValidator;
    public function arrayVal() : \Builderius\Respect\Validation\ChainedValidator;
    public function attribute(string $reference, ?\Builderius\Respect\Validation\Validatable $validator = null, bool $mandatory = \true) : \Builderius\Respect\Validation\ChainedValidator;
    public function base(int $base, ?string $chars = null) : \Builderius\Respect\Validation\ChainedValidator;
    public function base64() : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $minimum
     * @param mixed $maximum
     */
    public function between($minimum, $maximum) : \Builderius\Respect\Validation\ChainedValidator;
    public function bic(string $countryCode) : \Builderius\Respect\Validation\ChainedValidator;
    public function boolType() : \Builderius\Respect\Validation\ChainedValidator;
    public function boolVal() : \Builderius\Respect\Validation\ChainedValidator;
    public function bsn() : \Builderius\Respect\Validation\ChainedValidator;
    public function call(callable $callable, \Builderius\Respect\Validation\Validatable $rule) : \Builderius\Respect\Validation\ChainedValidator;
    public function callableType() : \Builderius\Respect\Validation\ChainedValidator;
    public function callback(callable $callback) : \Builderius\Respect\Validation\ChainedValidator;
    public function charset(string ...$charset) : \Builderius\Respect\Validation\ChainedValidator;
    public function cnh() : \Builderius\Respect\Validation\ChainedValidator;
    public function cnpj() : \Builderius\Respect\Validation\ChainedValidator;
    public function control(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public function consonant(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $containsValue
     */
    public function contains($containsValue, bool $identical = \false) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed[] $needles
     */
    public function containsAny(array $needles, bool $strictCompareArray = \false) : \Builderius\Respect\Validation\ChainedValidator;
    public function countable() : \Builderius\Respect\Validation\ChainedValidator;
    public function countryCode(?string $set = null) : \Builderius\Respect\Validation\ChainedValidator;
    public function currencyCode() : \Builderius\Respect\Validation\ChainedValidator;
    public function cpf() : \Builderius\Respect\Validation\ChainedValidator;
    public function creditCard(?string $brand = null) : \Builderius\Respect\Validation\ChainedValidator;
    public function date(string $format = 'Y-m-d') : \Builderius\Respect\Validation\ChainedValidator;
    public function dateTime(?string $format = null) : \Builderius\Respect\Validation\ChainedValidator;
    public function decimal(int $decimals) : \Builderius\Respect\Validation\ChainedValidator;
    public function digit(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public function directory() : \Builderius\Respect\Validation\ChainedValidator;
    public function domain(bool $tldCheck = \true) : \Builderius\Respect\Validation\ChainedValidator;
    public function each(\Builderius\Respect\Validation\Validatable $rule) : \Builderius\Respect\Validation\ChainedValidator;
    public function email() : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $endValue
     */
    public function endsWith($endValue, bool $identical = \false) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $compareTo
     */
    public function equals($compareTo) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $compareTo
     */
    public function equivalent($compareTo) : \Builderius\Respect\Validation\ChainedValidator;
    public function even() : \Builderius\Respect\Validation\ChainedValidator;
    public function executable() : \Builderius\Respect\Validation\ChainedValidator;
    public function exists() : \Builderius\Respect\Validation\ChainedValidator;
    public function extension(string $extension) : \Builderius\Respect\Validation\ChainedValidator;
    public function factor(int $dividend) : \Builderius\Respect\Validation\ChainedValidator;
    public function falseVal() : \Builderius\Respect\Validation\ChainedValidator;
    public function fibonacci() : \Builderius\Respect\Validation\ChainedValidator;
    public function file() : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed[]|int $options
     */
    public function filterVar(int $filter, $options = null) : \Builderius\Respect\Validation\ChainedValidator;
    public function finite() : \Builderius\Respect\Validation\ChainedValidator;
    public function floatVal() : \Builderius\Respect\Validation\ChainedValidator;
    public function floatType() : \Builderius\Respect\Validation\ChainedValidator;
    public function graph(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $compareTo
     */
    public function greaterThan($compareTo) : \Builderius\Respect\Validation\ChainedValidator;
    public function hexRgbColor() : \Builderius\Respect\Validation\ChainedValidator;
    public function iban() : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $compareTo
     */
    public function identical($compareTo) : \Builderius\Respect\Validation\ChainedValidator;
    public function image(?\finfo $fileInfo = null) : \Builderius\Respect\Validation\ChainedValidator;
    public function imei() : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed[]|mixed $haystack
     */
    public function in($haystack, bool $compareIdentical = \false) : \Builderius\Respect\Validation\ChainedValidator;
    public function infinite() : \Builderius\Respect\Validation\ChainedValidator;
    public function instance(string $instanceName) : \Builderius\Respect\Validation\ChainedValidator;
    public function intVal() : \Builderius\Respect\Validation\ChainedValidator;
    public function intType() : \Builderius\Respect\Validation\ChainedValidator;
    public function ip(string $range = '*', ?int $options = null) : \Builderius\Respect\Validation\ChainedValidator;
    public function isbn() : \Builderius\Respect\Validation\ChainedValidator;
    public function iterableType() : \Builderius\Respect\Validation\ChainedValidator;
    public function json() : \Builderius\Respect\Validation\ChainedValidator;
    public function key(string $reference, ?\Builderius\Respect\Validation\Validatable $referenceValidator = null, bool $mandatory = \true) : \Builderius\Respect\Validation\ChainedValidator;
    public function keyNested(string $reference, ?\Builderius\Respect\Validation\Validatable $referenceValidator = null, bool $mandatory = \true) : \Builderius\Respect\Validation\ChainedValidator;
    public function keySet(\Builderius\Respect\Validation\Rules\Key ...$rule) : \Builderius\Respect\Validation\ChainedValidator;
    public function keyValue(string $comparedKey, string $ruleName, string $baseKey) : \Builderius\Respect\Validation\ChainedValidator;
    public function languageCode(?string $set = null) : \Builderius\Respect\Validation\ChainedValidator;
    public function leapDate(string $format) : \Builderius\Respect\Validation\ChainedValidator;
    public function leapYear() : \Builderius\Respect\Validation\ChainedValidator;
    public function length(?int $min = null, ?int $max = null, bool $inclusive = \true) : \Builderius\Respect\Validation\ChainedValidator;
    public function lowercase() : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $compareTo
     */
    public function lessThan($compareTo) : \Builderius\Respect\Validation\ChainedValidator;
    public function luhn() : \Builderius\Respect\Validation\ChainedValidator;
    public function macAddress() : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $compareTo
     */
    public function max($compareTo) : \Builderius\Respect\Validation\ChainedValidator;
    public function maxAge(int $age, ?string $format = null) : \Builderius\Respect\Validation\ChainedValidator;
    public function mimetype(string $mimetype) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $compareTo
     */
    public function min($compareTo) : \Builderius\Respect\Validation\ChainedValidator;
    public function minAge(int $age, ?string $format = null) : \Builderius\Respect\Validation\ChainedValidator;
    public function multiple(int $multipleOf) : \Builderius\Respect\Validation\ChainedValidator;
    public function negative() : \Builderius\Respect\Validation\ChainedValidator;
    public function nfeAccessKey() : \Builderius\Respect\Validation\ChainedValidator;
    public function nif() : \Builderius\Respect\Validation\ChainedValidator;
    public function nip() : \Builderius\Respect\Validation\ChainedValidator;
    public function no(bool $useLocale = \false) : \Builderius\Respect\Validation\ChainedValidator;
    public function noneOf(\Builderius\Respect\Validation\Validatable ...$rule) : \Builderius\Respect\Validation\ChainedValidator;
    public function not(\Builderius\Respect\Validation\Validatable $rule) : \Builderius\Respect\Validation\ChainedValidator;
    public function notBlank() : \Builderius\Respect\Validation\ChainedValidator;
    public function notEmoji() : \Builderius\Respect\Validation\ChainedValidator;
    public function notEmpty() : \Builderius\Respect\Validation\ChainedValidator;
    public function notOptional() : \Builderius\Respect\Validation\ChainedValidator;
    public function noWhitespace() : \Builderius\Respect\Validation\ChainedValidator;
    public function nullable(\Builderius\Respect\Validation\Validatable $rule) : \Builderius\Respect\Validation\ChainedValidator;
    public function nullType() : \Builderius\Respect\Validation\ChainedValidator;
    public function number() : \Builderius\Respect\Validation\ChainedValidator;
    public function numericVal() : \Builderius\Respect\Validation\ChainedValidator;
    public function objectType() : \Builderius\Respect\Validation\ChainedValidator;
    public function odd() : \Builderius\Respect\Validation\ChainedValidator;
    public function oneOf(\Builderius\Respect\Validation\Validatable ...$rule) : \Builderius\Respect\Validation\ChainedValidator;
    public function optional(\Builderius\Respect\Validation\Validatable $rule) : \Builderius\Respect\Validation\ChainedValidator;
    public function perfectSquare() : \Builderius\Respect\Validation\ChainedValidator;
    public function pesel() : \Builderius\Respect\Validation\ChainedValidator;
    public function phone() : \Builderius\Respect\Validation\ChainedValidator;
    public function phpLabel() : \Builderius\Respect\Validation\ChainedValidator;
    public function pis() : \Builderius\Respect\Validation\ChainedValidator;
    public function polishIdCard() : \Builderius\Respect\Validation\ChainedValidator;
    public function positive() : \Builderius\Respect\Validation\ChainedValidator;
    public function postalCode(string $countryCode) : \Builderius\Respect\Validation\ChainedValidator;
    public function primeNumber() : \Builderius\Respect\Validation\ChainedValidator;
    public function printable(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public function punct(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public function readable() : \Builderius\Respect\Validation\ChainedValidator;
    public function regex(string $regex) : \Builderius\Respect\Validation\ChainedValidator;
    public function resourceType() : \Builderius\Respect\Validation\ChainedValidator;
    public function roman() : \Builderius\Respect\Validation\ChainedValidator;
    public function scalarVal() : \Builderius\Respect\Validation\ChainedValidator;
    public function sf(\Builderius\Symfony\Component\Validator\Constraint $constraint, ?\Builderius\Symfony\Component\Validator\Validator\ValidatorInterface $validator = null) : \Builderius\Respect\Validation\ChainedValidator;
    public function size(?string $minSize = null, ?string $maxSize = null) : \Builderius\Respect\Validation\ChainedValidator;
    public function slug() : \Builderius\Respect\Validation\ChainedValidator;
    public function sorted(string $direction) : \Builderius\Respect\Validation\ChainedValidator;
    public function space(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $startValue
     */
    public function startsWith($startValue, bool $identical = \false) : \Builderius\Respect\Validation\ChainedValidator;
    public function stringType() : \Builderius\Respect\Validation\ChainedValidator;
    public function stringVal() : \Builderius\Respect\Validation\ChainedValidator;
    public function subdivisionCode(string $countryCode) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed[] $superset
     */
    public function subset(array $superset) : \Builderius\Respect\Validation\ChainedValidator;
    public function symbolicLink() : \Builderius\Respect\Validation\ChainedValidator;
    public function time(string $format = 'H:i:s') : \Builderius\Respect\Validation\ChainedValidator;
    public function tld() : \Builderius\Respect\Validation\ChainedValidator;
    public function trueVal() : \Builderius\Respect\Validation\ChainedValidator;
    public function type(string $type) : \Builderius\Respect\Validation\ChainedValidator;
    public function unique() : \Builderius\Respect\Validation\ChainedValidator;
    public function uploaded() : \Builderius\Respect\Validation\ChainedValidator;
    public function uppercase() : \Builderius\Respect\Validation\ChainedValidator;
    public function url() : \Builderius\Respect\Validation\ChainedValidator;
    public function uuid(?int $version = null) : \Builderius\Respect\Validation\ChainedValidator;
    public function version() : \Builderius\Respect\Validation\ChainedValidator;
    public function videoUrl(?string $service = null) : \Builderius\Respect\Validation\ChainedValidator;
    public function vowel(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public function when(\Builderius\Respect\Validation\Validatable $if, \Builderius\Respect\Validation\Validatable $then, ?\Builderius\Respect\Validation\Validatable $else = null) : \Builderius\Respect\Validation\ChainedValidator;
    public function writable() : \Builderius\Respect\Validation\ChainedValidator;
    public function xdigit(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public function yes(bool $useLocale = \false) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param string|ZendValidator $validator
     * @param mixed[] $params
     */
    public function zend($validator, ?array $params = null) : \Builderius\Respect\Validation\ChainedValidator;
}
