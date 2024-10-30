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
use Builderius\Respect\Validation\Helpers\CanValidateDateTime;
use function date;
use function is_scalar;
use function preg_match;
use function sprintf;
use function strtotime;
/**
 * Validates whether an input is a time or not
 *
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
final class Time extends \Builderius\Respect\Validation\Rules\AbstractRule
{
    use CanValidateDateTime;
    /**
     * @var string
     */
    private $format;
    /**
     * @var string
     */
    private $sample;
    /**
     * Initializes the rule.
     *
     * @throws ComponentException
     */
    public function __construct(string $format = 'H:i:s')
    {
        if (!\preg_match('/^[gGhHisuvaA\\W]+$/', $format)) {
            throw new \Builderius\Respect\Validation\Exceptions\ComponentException(\sprintf('"%s" is not a valid date format', $format));
        }
        $this->format = $format;
        $this->sample = \date($format, \strtotime('23:59:59'));
    }
    /**
     * {@inheritDoc}
     */
    public function validate($input) : bool
    {
        if (!\is_scalar($input)) {
            return \false;
        }
        return $this->isDateTime($this->format, (string) $input);
    }
}
