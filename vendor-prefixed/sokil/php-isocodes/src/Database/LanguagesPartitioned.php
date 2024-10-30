<?php

declare (strict_types=1);
namespace Builderius\Sokil\IsoCodes\Database;

use Builderius\Sokil\IsoCodes\AbstractPartitionedDatabase;
use Builderius\Sokil\IsoCodes\Database\Languages\Language;
class LanguagesPartitioned extends \Builderius\Sokil\IsoCodes\AbstractPartitionedDatabase implements \Builderius\Sokil\IsoCodes\Database\LanguagesInterface
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
    public function getByAlpha2(string $alpha2) : ?\Builderius\Sokil\IsoCodes\Database\Languages\Language
    {
        $language = null;
        foreach ($this->loadFromJSONFile('/alpha2/' . $alpha2[0]) as $languageRaw) {
            if ($languageRaw['alpha_2'] === $alpha2) {
                $language = $this->arrayToEntry($languageRaw);
            }
        }
        return $language;
    }
    public function getByAlpha3(string $alpha3) : ?\Builderius\Sokil\IsoCodes\Database\Languages\Language
    {
        $language = null;
        foreach ($this->loadFromJSONFile('/alpha3/' . \substr($alpha3, 0, 2)) as $languageRaw) {
            if ($languageRaw['alpha_3'] === $alpha3) {
                $language = $this->arrayToEntry($languageRaw);
            }
        }
        return $language;
    }
}
