<?php

declare (strict_types=1);
namespace Builderius\Sokil\IsoCodes\Database;

use Builderius\Sokil\IsoCodes\Database\Subdivisions\Subdivision;
interface SubdivisionsInterface extends \Iterator, \Countable
{
    /**
     * @param string $subdivisionCode in format "alpha2country-subdivision", e.g. "UA-43"
     */
    public function getByCode(string $subdivisionCode) : ?\Builderius\Sokil\IsoCodes\Database\Subdivisions\Subdivision;
    /**
     * @param string $alpha2CountryCode e.g. "UA"
     *
     * @return Subdivision[]
     */
    public function getAllByCountryCode(string $alpha2CountryCode) : array;
}
