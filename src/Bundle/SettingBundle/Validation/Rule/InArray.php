<?php

namespace Builderius\Bundle\SettingBundle\Validation\Rule;

use Builderius\Respect\Validation\Rules\AbstractRule;
use Builderius\Respect\Validation\Rules\In;

class InArray extends AbstractRule
{
    /**
     * @var mixed[]|string
     */
    protected $haystack;

    /**
     * @var In
     */
    protected $parentValidator;

    /**
     * Initializes the rule with the haystack and optionally compareIdentical flag.
     *
     * @param mixed[]|string $haystack
     * @param bool $compareIdentical
     */
    public function __construct($haystack, $compareIdentical = false)
    {
        $this->haystack = $haystack;
        $this->parentValidator = new In($haystack, $compareIdentical);
    }

    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        return $this->parentValidator->validate($input);
    }
}