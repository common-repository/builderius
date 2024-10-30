<?php

declare (strict_types=1);
namespace Builderius\Sokil\IsoCodes\Database;

use Builderius\Sokil\IsoCodes\AbstractPartitionedDatabase;
use Builderius\Sokil\IsoCodes\Database\Subdivisions\Subdivision;
class SubdivisionsPartitioned extends \Builderius\Sokil\IsoCodes\AbstractPartitionedDatabase implements \Builderius\Sokil\IsoCodes\Database\SubdivisionsInterface
{
    /**
     * ISO Standard Number
     *
     * @psalm-pure
     */
    public static function getISONumber() : string
    {
        return '3166-2';
    }
    /**
     * @param array<string, string> $entry
     */
    protected function arrayToEntry(array $entry) : \Builderius\Sokil\IsoCodes\Database\Subdivisions\Subdivision
    {
        return new \Builderius\Sokil\IsoCodes\Database\Subdivisions\Subdivision($this->translationDriver, $entry['name'], $entry['code'], $entry['type'], !empty($entry['parent']) ? $entry['parent'] : null);
    }
    /**
     * @param string $subdivisionCode in format "alpha2country-subdivision", e.g. "UA-43"
     */
    public function getByCode(string $subdivisionCode) : ?\Builderius\Sokil\IsoCodes\Database\Subdivisions\Subdivision
    {
        if (\strpos($subdivisionCode, '-') === \false) {
            return null;
        }
        [$alpha2CountryCode] = \explode('-', $subdivisionCode);
        return $this->getAllByCountryCode($alpha2CountryCode)[$subdivisionCode] ?? null;
    }
    /**
     * @param string $alpha2CountryCode e.g. "UA"
     *
     * @return Subdivision[]
     */
    public function getAllByCountryCode(string $alpha2CountryCode) : array
    {
        $subdivisions = [];
        foreach ($this->loadFromJSONFile($alpha2CountryCode) as $subdivision) {
            $subdivisions[$subdivision['code']] = $this->arrayToEntry($subdivision);
        }
        return $subdivisions;
    }
}
