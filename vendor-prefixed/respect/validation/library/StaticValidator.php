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
interface StaticValidator
{
    public static function allOf(\Builderius\Respect\Validation\Validatable ...$rule) : \Builderius\Respect\Validation\ChainedValidator;
    public static function alnum(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public static function alpha(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public static function alwaysInvalid() : \Builderius\Respect\Validation\ChainedValidator;
    public static function alwaysValid() : \Builderius\Respect\Validation\ChainedValidator;
    public static function anyOf(\Builderius\Respect\Validation\Validatable ...$rule) : \Builderius\Respect\Validation\ChainedValidator;
    public static function arrayType() : \Builderius\Respect\Validation\ChainedValidator;
    public static function arrayVal() : \Builderius\Respect\Validation\ChainedValidator;
    public static function attribute(string $reference, ?\Builderius\Respect\Validation\Validatable $validator = null, bool $mandatory = \true) : \Builderius\Respect\Validation\ChainedValidator;
    public static function base(int $base, ?string $chars = null) : \Builderius\Respect\Validation\ChainedValidator;
    public static function base64() : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $minimum
     * @param mixed $maximum
     */
    public static function between($minimum, $maximum) : \Builderius\Respect\Validation\ChainedValidator;
    public static function bic(string $countryCode) : \Builderius\Respect\Validation\ChainedValidator;
    public static function boolType() : \Builderius\Respect\Validation\ChainedValidator;
    public static function boolVal() : \Builderius\Respect\Validation\ChainedValidator;
    public static function bsn() : \Builderius\Respect\Validation\ChainedValidator;
    public static function call(callable $callable, \Builderius\Respect\Validation\Validatable $rule) : \Builderius\Respect\Validation\ChainedValidator;
    public static function callableType() : \Builderius\Respect\Validation\ChainedValidator;
    public static function callback(callable $callback) : \Builderius\Respect\Validation\ChainedValidator;
    public static function charset(string ...$charset) : \Builderius\Respect\Validation\ChainedValidator;
    public static function cnh() : \Builderius\Respect\Validation\ChainedValidator;
    public static function cnpj() : \Builderius\Respect\Validation\ChainedValidator;
    public static function control(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public static function consonant(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $containsValue
     */
    public static function contains($containsValue, bool $identical = \false) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed[] $needles
     */
    public static function containsAny(array $needles, bool $strictCompareArray = \false) : \Builderius\Respect\Validation\ChainedValidator;
    public static function countable() : \Builderius\Respect\Validation\ChainedValidator;
    public static function countryCode(?string $set = null) : \Builderius\Respect\Validation\ChainedValidator;
    public static function currencyCode() : \Builderius\Respect\Validation\ChainedValidator;
    public static function cpf() : \Builderius\Respect\Validation\ChainedValidator;
    public static function creditCard(?string $brand = null) : \Builderius\Respect\Validation\ChainedValidator;
    public static function date(string $format = 'Y-m-d') : \Builderius\Respect\Validation\ChainedValidator;
    public static function dateTime(?string $format = null) : \Builderius\Respect\Validation\ChainedValidator;
    public static function decimal(int $decimals) : \Builderius\Respect\Validation\ChainedValidator;
    public static function digit(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public static function directory() : \Builderius\Respect\Validation\ChainedValidator;
    public static function domain(bool $tldCheck = \true) : \Builderius\Respect\Validation\ChainedValidator;
    public static function each(\Builderius\Respect\Validation\Validatable $rule) : \Builderius\Respect\Validation\ChainedValidator;
    public static function email() : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $endValue
     */
    public static function endsWith($endValue, bool $identical = \false) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $compareTo
     */
    public static function equals($compareTo) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $compareTo
     */
    public static function equivalent($compareTo) : \Builderius\Respect\Validation\ChainedValidator;
    public static function even() : \Builderius\Respect\Validation\ChainedValidator;
    public static function executable() : \Builderius\Respect\Validation\ChainedValidator;
    public static function exists() : \Builderius\Respect\Validation\ChainedValidator;
    public static function extension(string $extension) : \Builderius\Respect\Validation\ChainedValidator;
    public static function factor(int $dividend) : \Builderius\Respect\Validation\ChainedValidator;
    public static function falseVal() : \Builderius\Respect\Validation\ChainedValidator;
    public static function fibonacci() : \Builderius\Respect\Validation\ChainedValidator;
    public static function file() : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed[]|int $options
     */
    public static function filterVar(int $filter, $options = null) : \Builderius\Respect\Validation\ChainedValidator;
    public static function finite() : \Builderius\Respect\Validation\ChainedValidator;
    public static function floatVal() : \Builderius\Respect\Validation\ChainedValidator;
    public static function floatType() : \Builderius\Respect\Validation\ChainedValidator;
    public static function graph(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $compareTo
     */
    public static function greaterThan($compareTo) : \Builderius\Respect\Validation\ChainedValidator;
    public static function hexRgbColor() : \Builderius\Respect\Validation\ChainedValidator;
    public static function iban() : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $compareTo
     */
    public static function identical($compareTo) : \Builderius\Respect\Validation\ChainedValidator;
    public static function image(?\finfo $fileInfo = null) : \Builderius\Respect\Validation\ChainedValidator;
    public static function imei() : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed[]|mixed $haystack
     */
    public static function in($haystack, bool $compareIdentical = \false) : \Builderius\Respect\Validation\ChainedValidator;
    public static function infinite() : \Builderius\Respect\Validation\ChainedValidator;
    public static function instance(string $instanceName) : \Builderius\Respect\Validation\ChainedValidator;
    public static function intVal() : \Builderius\Respect\Validation\ChainedValidator;
    public static function intType() : \Builderius\Respect\Validation\ChainedValidator;
    public static function ip(string $range = '*', ?int $options = null) : \Builderius\Respect\Validation\ChainedValidator;
    public static function isbn() : \Builderius\Respect\Validation\ChainedValidator;
    public static function iterableType() : \Builderius\Respect\Validation\ChainedValidator;
    public static function json() : \Builderius\Respect\Validation\ChainedValidator;
    public static function key(string $reference, ?\Builderius\Respect\Validation\Validatable $referenceValidator = null, bool $mandatory = \true) : \Builderius\Respect\Validation\ChainedValidator;
    public static function keyNested(string $reference, ?\Builderius\Respect\Validation\Validatable $referenceValidator = null, bool $mandatory = \true) : \Builderius\Respect\Validation\ChainedValidator;
    public static function keySet(\Builderius\Respect\Validation\Rules\Key ...$rule) : \Builderius\Respect\Validation\ChainedValidator;
    public static function keyValue(string $comparedKey, string $ruleName, string $baseKey) : \Builderius\Respect\Validation\ChainedValidator;
    public static function languageCode(?string $set = null) : \Builderius\Respect\Validation\ChainedValidator;
    public static function leapDate(string $format) : \Builderius\Respect\Validation\ChainedValidator;
    public static function leapYear() : \Builderius\Respect\Validation\ChainedValidator;
    public static function length(?int $min = null, ?int $max = null, bool $inclusive = \true) : \Builderius\Respect\Validation\ChainedValidator;
    public static function lowercase() : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $compareTo
     */
    public static function lessThan($compareTo) : \Builderius\Respect\Validation\ChainedValidator;
    public static function luhn() : \Builderius\Respect\Validation\ChainedValidator;
    public static function macAddress() : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $compareTo
     */
    public static function max($compareTo) : \Builderius\Respect\Validation\ChainedValidator;
    public static function maxAge(int $age, ?string $format = null) : \Builderius\Respect\Validation\ChainedValidator;
    public static function mimetype(string $mimetype) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $compareTo
     */
    public static function min($compareTo) : \Builderius\Respect\Validation\ChainedValidator;
    public static function minAge(int $age, ?string $format = null) : \Builderius\Respect\Validation\ChainedValidator;
    public static function multiple(int $multipleOf) : \Builderius\Respect\Validation\ChainedValidator;
    public static function negative() : \Builderius\Respect\Validation\ChainedValidator;
    public static function nfeAccessKey() : \Builderius\Respect\Validation\ChainedValidator;
    public static function nif() : \Builderius\Respect\Validation\ChainedValidator;
    public static function nip() : \Builderius\Respect\Validation\ChainedValidator;
    public static function no(bool $useLocale = \false) : \Builderius\Respect\Validation\ChainedValidator;
    public static function noneOf(\Builderius\Respect\Validation\Validatable ...$rule) : \Builderius\Respect\Validation\ChainedValidator;
    public static function not(\Builderius\Respect\Validation\Validatable $rule) : \Builderius\Respect\Validation\ChainedValidator;
    public static function notBlank() : \Builderius\Respect\Validation\ChainedValidator;
    public static function notEmoji() : \Builderius\Respect\Validation\ChainedValidator;
    public static function notEmpty() : \Builderius\Respect\Validation\ChainedValidator;
    public static function notOptional() : \Builderius\Respect\Validation\ChainedValidator;
    public static function noWhitespace() : \Builderius\Respect\Validation\ChainedValidator;
    public static function nullable(\Builderius\Respect\Validation\Validatable $rule) : \Builderius\Respect\Validation\ChainedValidator;
    public static function nullType() : \Builderius\Respect\Validation\ChainedValidator;
    public static function number() : \Builderius\Respect\Validation\ChainedValidator;
    public static function numericVal() : \Builderius\Respect\Validation\ChainedValidator;
    public static function objectType() : \Builderius\Respect\Validation\ChainedValidator;
    public static function odd() : \Builderius\Respect\Validation\ChainedValidator;
    public static function oneOf(\Builderius\Respect\Validation\Validatable ...$rule) : \Builderius\Respect\Validation\ChainedValidator;
    public static function optional(\Builderius\Respect\Validation\Validatable $rule) : \Builderius\Respect\Validation\ChainedValidator;
    public static function perfectSquare() : \Builderius\Respect\Validation\ChainedValidator;
    public static function pesel() : \Builderius\Respect\Validation\ChainedValidator;
    public static function phone() : \Builderius\Respect\Validation\ChainedValidator;
    public static function phpLabel() : \Builderius\Respect\Validation\ChainedValidator;
    public static function pis() : \Builderius\Respect\Validation\ChainedValidator;
    public static function polishIdCard() : \Builderius\Respect\Validation\ChainedValidator;
    public static function positive() : \Builderius\Respect\Validation\ChainedValidator;
    public static function postalCode(string $countryCode) : \Builderius\Respect\Validation\ChainedValidator;
    public static function primeNumber() : \Builderius\Respect\Validation\ChainedValidator;
    public static function printable(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public static function punct(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public static function readable() : \Builderius\Respect\Validation\ChainedValidator;
    public static function regex(string $regex) : \Builderius\Respect\Validation\ChainedValidator;
    public static function resourceType() : \Builderius\Respect\Validation\ChainedValidator;
    public static function roman() : \Builderius\Respect\Validation\ChainedValidator;
    public static function scalarVal() : \Builderius\Respect\Validation\ChainedValidator;
    public static function sf(\Builderius\Symfony\Component\Validator\Constraint $constraint, ?\Builderius\Symfony\Component\Validator\Validator\ValidatorInterface $validator = null) : \Builderius\Respect\Validation\ChainedValidator;
    public static function size(?string $minSize = null, ?string $maxSize = null) : \Builderius\Respect\Validation\ChainedValidator;
    public static function slug() : \Builderius\Respect\Validation\ChainedValidator;
    public static function sorted(string $direction) : \Builderius\Respect\Validation\ChainedValidator;
    public static function space(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed $startValue
     */
    public static function startsWith($startValue, bool $identical = \false) : \Builderius\Respect\Validation\ChainedValidator;
    public static function stringType() : \Builderius\Respect\Validation\ChainedValidator;
    public static function stringVal() : \Builderius\Respect\Validation\ChainedValidator;
    public static function subdivisionCode(string $countryCode) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param mixed[] $superset
     */
    public static function subset(array $superset) : \Builderius\Respect\Validation\ChainedValidator;
    public static function symbolicLink() : \Builderius\Respect\Validation\ChainedValidator;
    public static function time(string $format = 'H:i:s') : \Builderius\Respect\Validation\ChainedValidator;
    public static function tld() : \Builderius\Respect\Validation\ChainedValidator;
    public static function trueVal() : \Builderius\Respect\Validation\ChainedValidator;
    public static function type(string $type) : \Builderius\Respect\Validation\ChainedValidator;
    public static function unique() : \Builderius\Respect\Validation\ChainedValidator;
    public static function uploaded() : \Builderius\Respect\Validation\ChainedValidator;
    public static function uppercase() : \Builderius\Respect\Validation\ChainedValidator;
    public static function url() : \Builderius\Respect\Validation\ChainedValidator;
    public static function uuid(?int $version = null) : \Builderius\Respect\Validation\ChainedValidator;
    public static function version() : \Builderius\Respect\Validation\ChainedValidator;
    public static function videoUrl(?string $service = null) : \Builderius\Respect\Validation\ChainedValidator;
    public static function vowel(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public static function when(\Builderius\Respect\Validation\Validatable $if, \Builderius\Respect\Validation\Validatable $then, ?\Builderius\Respect\Validation\Validatable $else = null) : \Builderius\Respect\Validation\ChainedValidator;
    public static function writable() : \Builderius\Respect\Validation\ChainedValidator;
    public static function xdigit(string ...$additionalChars) : \Builderius\Respect\Validation\ChainedValidator;
    public static function yes(bool $useLocale = \false) : \Builderius\Respect\Validation\ChainedValidator;
    /**
     * @param string|ZendValidator $validator
     * @param mixed[] $params
     */
    public static function zend($validator, ?array $params = null) : \Builderius\Respect\Validation\ChainedValidator;
}
