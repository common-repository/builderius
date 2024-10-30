<?php

declare (strict_types=1);
namespace Builderius\Sokil\IsoCodes;

use Builderius\Sokil\IsoCodes\Database\Countries;
use Builderius\Sokil\IsoCodes\Database\Currencies;
use Builderius\Sokil\IsoCodes\Database\HistoricCountries;
use Builderius\Sokil\IsoCodes\Database\Languages;
use Builderius\Sokil\IsoCodes\Database\LanguagesInterface;
use Builderius\Sokil\IsoCodes\Database\LanguagesPartitioned;
use Builderius\Sokil\IsoCodes\Database\Scripts;
use Builderius\Sokil\IsoCodes\Database\Subdivisions;
use Builderius\Sokil\IsoCodes\Database\SubdivisionsInterface;
use Builderius\Sokil\IsoCodes\Database\SubdivisionsPartitioned;
use Builderius\Sokil\IsoCodes\TranslationDriver\GettextExtensionDriver;
use Builderius\Sokil\IsoCodes\TranslationDriver\TranslationDriverInterface;
/**
 * Factory class to build ISO databases
 */
class IsoCodesFactory
{
    /**
     * Database splits into partition files.
     *
     * Fetching some entry will load only little part of database.
     * Loaded entries not stored statically.
     *
     * This scenario may be useful when just few entries need
     * to be loaded, for example on web request when one entry fetched.
     *
     * This may require a lot of file read operations.
     */
    public const OPTIMISATION_MEMORY = 1;
    /**
     * Entire database loaded into memory from single JSON file once.
     *
     * All entries created and stored into RAM. Next read of save
     * entry will just return it without io operations with files and building objects.
     *
     * This scenario may be useful for daemons to decrease file operations,
     * or when most entries will be fetched from database.
     *
     * This may require a lot of RAM for storing all entries.
     */
    public const OPTIMISATION_IO = 2;
    /**
     * Path to directory with databases
     *
     * @var string
     */
    private $baseDirectory;
    /**
     * @var TranslationDriverInterface
     */
    private $translationDriver;
    public function __construct(string $baseDirectory = null, \Builderius\Sokil\IsoCodes\TranslationDriver\TranslationDriverInterface $translationDriver = null)
    {
        $this->baseDirectory = $baseDirectory;
        $this->translationDriver = $translationDriver ?? new \Builderius\Sokil\IsoCodes\TranslationDriver\GettextExtensionDriver();
    }
    /**
     * ISO 3166-1
     */
    public function getCountries() : \Builderius\Sokil\IsoCodes\Database\Countries
    {
        return new \Builderius\Sokil\IsoCodes\Database\Countries($this->baseDirectory, $this->translationDriver);
    }
    /**
     * ISO 3166-2
     *
     * @param int $optimisation One of self::OPTIMISATION_* constants
     *
     * @throws \InvalidArgumentException When invalid optimisation specified
     */
    public function getSubdivisions(int $optimisation = self::OPTIMISATION_MEMORY) : \Builderius\Sokil\IsoCodes\Database\SubdivisionsInterface
    {
        switch ($optimisation) {
            case self::OPTIMISATION_MEMORY:
                $database = new \Builderius\Sokil\IsoCodes\Database\SubdivisionsPartitioned($this->baseDirectory, $this->translationDriver);
                break;
            case self::OPTIMISATION_IO:
                $database = new \Builderius\Sokil\IsoCodes\Database\Subdivisions($this->baseDirectory, $this->translationDriver);
                break;
            default:
                throw new \InvalidArgumentException('Invalid optimisation specified');
        }
        return $database;
    }
    /**
     * ISO 3166-3
     */
    public function getHistoricCountries() : \Builderius\Sokil\IsoCodes\Database\HistoricCountries
    {
        return new \Builderius\Sokil\IsoCodes\Database\HistoricCountries($this->baseDirectory, $this->translationDriver);
    }
    /**
     * ISO 15924
     */
    public function getScripts() : \Builderius\Sokil\IsoCodes\Database\Scripts
    {
        return new \Builderius\Sokil\IsoCodes\Database\Scripts($this->baseDirectory, $this->translationDriver);
    }
    /**
     * ISO 4217
     */
    public function getCurrencies() : \Builderius\Sokil\IsoCodes\Database\Currencies
    {
        return new \Builderius\Sokil\IsoCodes\Database\Currencies($this->baseDirectory, $this->translationDriver);
    }
    /**
     * ISO 639-3
     *
     * @param int $optimisation One of self::OPTIMISATION_* constants
     *
     * @throws \InvalidArgumentException When invalid optimisation specified
     */
    public function getLanguages(int $optimisation = self::OPTIMISATION_MEMORY) : \Builderius\Sokil\IsoCodes\Database\LanguagesInterface
    {
        switch ($optimisation) {
            case self::OPTIMISATION_MEMORY:
                $database = new \Builderius\Sokil\IsoCodes\Database\LanguagesPartitioned($this->baseDirectory, $this->translationDriver);
                break;
            case self::OPTIMISATION_IO:
                $database = new \Builderius\Sokil\IsoCodes\Database\Languages($this->baseDirectory, $this->translationDriver);
                break;
            default:
                throw new \InvalidArgumentException('Invalid optimisation specified');
        }
        return $database;
    }
}
