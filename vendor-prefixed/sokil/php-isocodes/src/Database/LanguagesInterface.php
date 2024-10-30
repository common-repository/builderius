<?php

declare (strict_types=1);
namespace Builderius\Sokil\IsoCodes\Database;

use Builderius\Sokil\IsoCodes\Database\Languages\Language;
interface LanguagesInterface extends \Iterator, \Countable
{
    public function getByAlpha2(string $alpha2) : ?\Builderius\Sokil\IsoCodes\Database\Languages\Language;
    public function getByAlpha3(string $alpha3) : ?\Builderius\Sokil\IsoCodes\Database\Languages\Language;
}
