<?php

declare (strict_types=1);
namespace Builderius\Sokil\IsoCodes\Database\Scripts;

use Builderius\Sokil\IsoCodes\Database\Scripts;
use Builderius\Sokil\IsoCodes\TranslationDriver\TranslatorInterface;
/**
 * @psalm-immutable
 */
class Script
{
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
     * @var string
     */
    private $alpha4;
    /**
     * @var string
     */
    private $numericCode;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    public function __construct(\Builderius\Sokil\IsoCodes\TranslationDriver\TranslatorInterface $translator, string $name, string $alpha4, string $numericCode)
    {
        $this->translator = $translator;
        $this->name = $name;
        $this->alpha4 = $alpha4;
        $this->numericCode = $numericCode;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getLocalName() : string
    {
        if ($this->localName === null) {
            $this->localName = $this->translator->translate(\Builderius\Sokil\IsoCodes\Database\Scripts::getISONumber(), $this->name);
        }
        return $this->localName;
    }
    public function getAlpha4() : string
    {
        return $this->alpha4;
    }
    public function getNumericCode() : string
    {
        return $this->numericCode;
    }
}
