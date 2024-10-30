<?php

declare (strict_types=1);
namespace Builderius\Sokil\IsoCodes\Database;

use Builderius\Sokil\IsoCodes\AbstractNotPartitionedDatabase;
use Builderius\Sokil\IsoCodes\Database\Currencies\Currency;
/**
 * @method Currency|null find(string $indexedFieldName, string $fieldValue)
 */
class Currencies extends \Builderius\Sokil\IsoCodes\AbstractNotPartitionedDatabase
{
    /**
     * ISO Standard Number
     *
     * @psalm-pure
     */
    public static function getISONumber() : string
    {
        return '4217';
    }
    /**
     * @param array<string, string> $entry
     */
    protected function arrayToEntry(array $entry) : \Builderius\Sokil\IsoCodes\Database\Currencies\Currency
    {
        return new \Builderius\Sokil\IsoCodes\Database\Currencies\Currency($this->translationDriver, $entry['name'], $entry['alpha_3'], $entry['numeric']);
    }
    /**
     * @return string[]
     */
    protected function getIndexDefinition() : array
    {
        return ['alpha_3', 'numeric'];
    }
    public function getByLetterCode(string $code) : ?\Builderius\Sokil\IsoCodes\Database\Currencies\Currency
    {
        return $this->find('alpha_3', $code);
    }
    /**
     * Using int code argument is deprecated due to it can be with leading 0 (e.g. '042').
     * Please, use numeric strings.
     *
     * @param string|int $code
     *
     * @return Currency|null
     *
     * @throws \TypeError
     */
    public function getByNumericCode($code) : ?\Builderius\Sokil\IsoCodes\Database\Currencies\Currency
    {
        if (!\is_numeric($code)) {
            throw new \TypeError('Argument must be int or string');
        }
        return $this->find('numeric', (string) $code);
    }
}
