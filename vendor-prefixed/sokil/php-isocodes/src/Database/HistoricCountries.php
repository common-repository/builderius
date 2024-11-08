<?php

declare (strict_types=1);
namespace Builderius\Sokil\IsoCodes\Database;

use Builderius\Sokil\IsoCodes\AbstractNotPartitionedDatabase;
use Builderius\Sokil\IsoCodes\Database\HistoricCountries\Country;
/**
 * @method Country|null find(string $indexedFieldName, string $fieldValue)
 */
class HistoricCountries extends \Builderius\Sokil\IsoCodes\AbstractNotPartitionedDatabase
{
    /**
     * ISO Standard Number
     *
     * @psalm-pure
     */
    public static function getISONumber() : string
    {
        return '3166-3';
    }
    /**
     * @param array<string, string> $entry
     */
    protected function arrayToEntry(array $entry) : \Builderius\Sokil\IsoCodes\Database\HistoricCountries\Country
    {
        return new \Builderius\Sokil\IsoCodes\Database\HistoricCountries\Country($this->translationDriver, $entry['name'], $entry['alpha_4'], $entry['alpha_3'], $entry['alpha_2'], $entry['withdrawal_date'], !empty($entry['numeric']) ? $entry['numeric'] : null);
    }
    /**
     * @return string[]
     */
    protected function getIndexDefinition() : array
    {
        return ['alpha_4', 'alpha_3', 'alpha_2', 'numeric'];
    }
    public function getByAlpha4(string $code) : ?\Builderius\Sokil\IsoCodes\Database\HistoricCountries\Country
    {
        return $this->find('alpha_4', $code);
    }
    public function getByAlpha3(string $code) : ?\Builderius\Sokil\IsoCodes\Database\HistoricCountries\Country
    {
        return $this->find('alpha_3', $code);
    }
    public function getByAlpha2(string $code) : ?\Builderius\Sokil\IsoCodes\Database\HistoricCountries\Country
    {
        return $this->find('alpha_2', $code);
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
    public function getByNumericCode($code) : ?\Builderius\Sokil\IsoCodes\Database\HistoricCountries\Country
    {
        if (!\is_numeric($code)) {
            throw new \TypeError('Argument must be int or string');
        }
        return $this->find('numeric', (string) $code);
    }
}
