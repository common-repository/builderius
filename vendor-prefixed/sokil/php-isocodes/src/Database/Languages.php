<?php

declare (strict_types=1);
namespace Builderius\Sokil\IsoCodes\Database;

use Builderius\Sokil\IsoCodes\AbstractNotPartitionedDatabase;
use Builderius\Sokil\IsoCodes\Database\Languages\Language;
/**
 * @method Language|null find(string $indexedFieldName, string $fieldValue)
 */
class Languages extends \Builderius\Sokil\IsoCodes\AbstractNotPartitionedDatabase implements \Builderius\Sokil\IsoCodes\Database\LanguagesInterface
{
    /**
     * ISO Standard Number
     *
     * @psalm-pure
     */
    public static function getISONumber() : string
    {
        return '639-3';
    }
    /**
     * @param array<string, string> $entry
     */
    protected function arrayToEntry(array $entry) : \Builderius\Sokil\IsoCodes\Database\Languages\Language
    {
        return new \Builderius\Sokil\IsoCodes\Database\Languages\Language($this->translationDriver, $entry['name'], $entry['alpha_3'], $entry['scope'], $entry['type'], !empty($entry['inverted_name']) ? $entry['inverted_name'] : null, !empty($entry['alpha_2']) ? $entry['alpha_2'] : null);
    }
    /**
     * @return string[]
     */
    protected function getIndexDefinition() : array
    {
        return ['alpha_2', 'alpha_3'];
    }
    public function getByAlpha2(string $alpha2) : ?\Builderius\Sokil\IsoCodes\Database\Languages\Language
    {
        return $this->find('alpha_2', $alpha2);
    }
    public function getByAlpha3(string $alpha3) : ?\Builderius\Sokil\IsoCodes\Database\Languages\Language
    {
        return $this->find('alpha_3', $alpha3);
    }
}
