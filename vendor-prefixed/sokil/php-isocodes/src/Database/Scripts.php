<?php

declare (strict_types=1);
namespace Builderius\Sokil\IsoCodes\Database;

use Builderius\Sokil\IsoCodes\AbstractNotPartitionedDatabase;
use Builderius\Sokil\IsoCodes\Database\Scripts\Script;
/**
 * @method Script|null find(string $indexedFieldName, string $fieldValue)
 */
class Scripts extends \Builderius\Sokil\IsoCodes\AbstractNotPartitionedDatabase
{
    /**
     * ISO Standard Number
     *
     * @psalm-pure
     */
    public static function getISONumber() : string
    {
        return '15924';
    }
    /**
     * @param array<string, string> $entry
     *
     */
    protected function arrayToEntry(array $entry) : \Builderius\Sokil\IsoCodes\Database\Scripts\Script
    {
        return new \Builderius\Sokil\IsoCodes\Database\Scripts\Script($this->translationDriver, $entry['name'], $entry['alpha_4'], $entry['numeric']);
    }
    /**
     * @return string[]
     */
    protected function getIndexDefinition() : array
    {
        return ['alpha_4', 'numeric'];
    }
    public function getByAlpha4(string $alpha4) : ?\Builderius\Sokil\IsoCodes\Database\Scripts\Script
    {
        return $this->find('alpha_4', $alpha4);
    }
    /**
     * Using int code argument is deprecated due to it can be with leading 0 (e.g. '042').
     * Please, use numeric strings.
     *
     * @param string|int $code
     *
     * @return Script|null
     *
     * @throws \TypeError
     */
    public function getByNumericCode($code) : ?\Builderius\Sokil\IsoCodes\Database\Scripts\Script
    {
        if (!\is_numeric($code)) {
            throw new \TypeError('Argument must be int or string');
        }
        return $this->find('numeric', (string) $code);
    }
}
