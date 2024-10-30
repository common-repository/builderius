<?php

declare (strict_types=1);
namespace Builderius\Sokil\IsoCodes\Database;

use Builderius\Sokil\IsoCodes\AbstractNotPartitionedDatabase;
use Builderius\Sokil\IsoCodes\Database\Subdivisions\Subdivision;
/**
 * @method Subdivision|Subdivision[]|null find(string $indexedFieldName, string $fieldValue)
 */
class Subdivisions extends \Builderius\Sokil\IsoCodes\AbstractNotPartitionedDatabase implements \Builderius\Sokil\IsoCodes\Database\SubdivisionsInterface
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
     * @return mixed[]
     */
    protected function getIndexDefinition() : array
    {
        return ['code', 'country_code' => [['code', 2], 'code']];
    }
    /**
     * @param string $subdivisionCode in format "alpha2country-subdivision", e.g. "UA-43"
     */
    public function getByCode(string $subdivisionCode) : ?\Builderius\Sokil\IsoCodes\Database\Subdivisions\Subdivision
    {
        return $this->find('code', $subdivisionCode);
    }
    /**
     * @param string $alpha2CountryCode e.g. "UA"
     *
     * @return Subdivision[]
     */
    public function getAllByCountryCode(string $alpha2CountryCode) : array
    {
        $subdivisions = $this->find('country_code', $alpha2CountryCode);
        if (empty($subdivisions)) {
            $subdivisions = [];
        }
        return $subdivisions;
    }
}
