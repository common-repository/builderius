<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\String\Slugger;

use Builderius\Symfony\Component\String\AbstractUnicodeString;
use Builderius\Symfony\Component\String\UnicodeString;
use Builderius\Symfony\Contracts\Translation\LocaleAwareInterface;
if (!\interface_exists(\Builderius\Symfony\Contracts\Translation\LocaleAwareInterface::class)) {
    throw new \LogicException('You cannot use the "Symfony\\Component\\String\\Slugger\\AsciiSlugger" as the "symfony/translation-contracts" package is not installed. Try running "composer require symfony/translation-contracts".');
}
/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @experimental in 5.0
 */
class AsciiSlugger implements \Builderius\Symfony\Component\String\Slugger\SluggerInterface, \Builderius\Symfony\Contracts\Translation\LocaleAwareInterface
{
    private const LOCALE_TO_TRANSLITERATOR_ID = ['am' => 'Amharic-Latin', 'ar' => 'Arabic-Latin', 'az' => 'Azerbaijani-Latin', 'be' => 'Belarusian-Latin', 'bg' => 'Bulgarian-Latin', 'bn' => 'Bengali-Latin', 'de' => 'de-ASCII', 'el' => 'Greek-Latin', 'fa' => 'Persian-Latin', 'he' => 'Hebrew-Latin', 'hy' => 'Armenian-Latin', 'ka' => 'Georgian-Latin', 'kk' => 'Kazakh-Latin', 'ky' => 'Kirghiz-Latin', 'ko' => 'Korean-Latin', 'mk' => 'Macedonian-Latin', 'mn' => 'Mongolian-Latin', 'or' => 'Oriya-Latin', 'ps' => 'Pashto-Latin', 'ru' => 'Russian-Latin', 'sr' => 'Serbian-Latin', 'sr_Cyrl' => 'Serbian-Latin', 'th' => 'Thai-Latin', 'tk' => 'Turkmen-Latin', 'uk' => 'Ukrainian-Latin', 'uz' => 'Uzbek-Latin', 'zh' => 'Han-Latin'];
    private $defaultLocale;
    /**
     * Cache of transliterators per locale.
     *
     * @var \Transliterator[]
     */
    private $transliterators = [];
    public function __construct(string $defaultLocale = null)
    {
        $this->defaultLocale = $defaultLocale;
    }
    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->defaultLocale = $locale;
    }
    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->defaultLocale;
    }
    /**
     * {@inheritdoc}
     */
    public function slug(string $string, string $separator = '-', string $locale = null) : \Builderius\Symfony\Component\String\AbstractUnicodeString
    {
        $locale = $locale ?? $this->defaultLocale;
        $transliterator = [];
        if ('de' === $locale || 0 === \strpos($locale, 'de_')) {
            // Use the shortcut for German in UnicodeString::ascii() if possible (faster and no requirement on intl)
            $transliterator = ['de-ASCII'];
        } elseif (\function_exists('transliterator_transliterate') && $locale) {
            $transliterator = (array) $this->createTransliterator($locale);
        }
        return (new \Builderius\Symfony\Component\String\UnicodeString($string))->ascii($transliterator)->replace('@', $separator . 'at' . $separator)->replaceMatches('/[^A-Za-z0-9]++/', $separator)->trim($separator);
    }
    private function createTransliterator(string $locale) : ?\Transliterator
    {
        if (\array_key_exists($locale, $this->transliterators)) {
            return $this->transliterators[$locale];
        }
        // Exact locale supported, cache and return
        if ($id = self::LOCALE_TO_TRANSLITERATOR_ID[$locale] ?? null) {
            return $this->transliterators[$locale] = \Transliterator::create($id . '/BGN') ?? \Transliterator::create($id);
        }
        // Locale not supported and no parent, fallback to any-latin
        if (\false === ($str = \strrchr($locale, '_'))) {
            return $this->transliterators[$locale] = null;
        }
        // Try to use the parent locale (ie. try "de" for "de_AT") and cache both locales
        $parent = \substr($locale, 0, -\strlen($str));
        if ($id = self::LOCALE_TO_TRANSLITERATOR_ID[$parent] ?? null) {
            $transliterator = \Transliterator::create($id . '/BGN') ?? \Transliterator::create($id);
        }
        return $this->transliterators[$locale] = $this->transliterators[$parent] = $transliterator ?? null;
    }
}
