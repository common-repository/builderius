<?php

declare (strict_types=1);
namespace Builderius\Sokil\IsoCodes\TranslationDriver;

interface TranslationDriverInterface extends \Builderius\Sokil\IsoCodes\TranslationDriver\TranslatorInterface
{
    public function configureDirectory(string $isoNumber, string $directory) : void;
    /**
     * @param string $locale
     */
    public function setLocale(string $locale) : void;
}
