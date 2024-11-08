<?php

/*
 * This file is part of Respect/Validation.
 *
 * (c) Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */
declare (strict_types=1);
namespace Builderius\Respect\Validation\Rules;

use Builderius\Respect\Validation\Exceptions\ComponentException;
use Builderius\Sokil\IsoCodes\IsoCodesFactory;
use Builderius\Sokil\IsoCodes\TranslationDriver\DummyDriver;
use function is_string;
use function sprintf;
/**
 * Validates country subdivision codes according to ISO 3166-2.
 *
 * @see http://en.wikipedia.org/wiki/ISO_3166-2
 * @see http://www.geonames.org/countries/
 *
 * @author Henrique Moody <henriquemoody@gmail.com>
 * @author Mazen Touati <mazen_touati@hotmail.com>
 */
final class SubdivisionCode extends \Builderius\Respect\Validation\Rules\AbstractRule
{
    /**
     * @var string
     */
    private $countryCode;
    /**
     * @var string
     */
    private $countryName;
    /**
     * @var IsoCodesFactory
     */
    private $factory;
    public function __construct(string $countryCode)
    {
        $factory = new \Builderius\Sokil\IsoCodes\IsoCodesFactory(null, new \Builderius\Sokil\IsoCodes\TranslationDriver\DummyDriver());
        $country = $factory->getCountries()->getByAlpha2($countryCode);
        if ($country === null) {
            throw new \Builderius\Respect\Validation\Exceptions\ComponentException(\sprintf('"%s" is not a supported country code', $countryCode));
        }
        $this->factory = $factory;
        $this->countryCode = $countryCode;
        $this->countryName = $country->getName();
    }
    /**
     * {@inheritDoc}
     */
    public function validate($input) : bool
    {
        if (!\is_string($input)) {
            return \false;
        }
        return $this->factory->getSubdivisions()->getByCode($this->countryCode . '-' . $input) !== null;
    }
}
