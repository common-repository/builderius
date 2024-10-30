<?php

declare (strict_types=1);
namespace Builderius\Sokil\IsoCodes\Database\Currencies;

use Builderius\Sokil\IsoCodes\Database\Currencies;
use Builderius\Sokil\IsoCodes\TranslationDriver\TranslatorInterface;
/**
 * @psalm-immutable
 */
class Currency
{
    /**
     * Alpha3
     *
     * @var string
     */
    private $letterCode;
    /**
     * @var string
     */
    private $numericCode;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string|null
     *
     * @psalm-allow-private-mutation
     */
    private $localName;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    public function __construct(\Builderius\Sokil\IsoCodes\TranslationDriver\TranslatorInterface $translator, string $name, string $letterCode, string $numericCode)
    {
        $this->translator = $translator;
        $this->name = $name;
        $this->letterCode = $letterCode;
        $this->numericCode = $numericCode;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getLocalName() : string
    {
        if ($this->localName === null) {
            $this->localName = $this->translator->translate(\Builderius\Sokil\IsoCodes\Database\Currencies::getISONumber(), $this->name);
        }
        return $this->localName;
    }
    public function getLetterCode() : string
    {
        return $this->letterCode;
    }
    public function getNumericCode() : string
    {
        return $this->numericCode;
    }
}
