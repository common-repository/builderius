<?php

declare (strict_types=1);
namespace Builderius\Sokil\IsoCodes\Database;

use Builderius\Sokil\IsoCodes\AbstractNotPartitionedDatabase;
use Builderius\Sokil\IsoCodes\Database\Countries\Country;
/**
 * @method Country|null find(string $indexedFieldName, string $fieldValue)
 */
class Countries extends \Builderius\Sokil\IsoCodes\AbstractNotPartitionedDatabase
{
    /**
     * ISO Standard Number
     *
     * @psalm-pure
     */
    public static function getISONumber() : string
    {
        return '3166-1';
    }
    /**
     * @param array<string, string> $entry
     */
    protected function arrayToEntry(array $entry) : \Builderius\Sokil\IsoCodes\Database\Countries\Country
    {
        return new \Builderius\Sokil\IsoCodes\Database\Countries\Country($this->translationDriver, $entry['name'], $entry['alpha_2'], $entry['alpha_3'], $entry['numeric'], !empty($entry['official_name']) ? $entry['official_name'] : null);
    }
    /**
     * @return string[]
     */
    protected function getIndexDefinition() : array
    {
        return ['alpha_2', 'alpha_3', 'numeric'];
    }
    public function getByAlpha2(string $alpha2) : ?\Builderius\Sokil\IsoCodes\Database\Countries\Country
    {
        return $this->find('alpha_2', $alpha2);
    }
    public function getByAlpha3(string $alpha3) : ?\Builderius\Sokil\IsoCodes\Database\Countries\Country
    {
        return $this->find('alpha_3', $alpha3);
    }
    /**
     * Using int code argument is deprecated due to it can be with leading 0 (e.g. '042').
     * Please, use numeric strings.
     *
     * @param string|int $code
     *
     * @return Country|null
     *
     * @throws \TypeError
     */
    public function getByNumericCode($code) : ?\Builderius\Sokil\IsoCodes\Database\Countries\Country
    {
        if (!\is_numeric($code)) {
            throw new \TypeError('Argument must be int or string');
        }
        return $this->find('numeric', (string) $code);
    }
}
