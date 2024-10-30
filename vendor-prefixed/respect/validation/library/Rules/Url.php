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
use const FILTER_VALIDATE_URL;
/**
 * Validates whether the input is a URL.
 *
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
final class Url extends \Builderius\Respect\Validation\Rules\AbstractEnvelope
{
    /**
     * Initializes the rule.
     *
     * @throws ComponentException
     */
    public function __construct()
    {
        parent::__construct(new \Builderius\Respect\Validation\Rules\FilterVar(\FILTER_VALIDATE_URL));
    }
}
