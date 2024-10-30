<?php

/*
 * This file is part of Respect/Stringifier.
 *
 * (c) Henrique Moody <henriquemoody@gmail.com>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */
declare (strict_types=1);
namespace Builderius\Respect\Stringifier\Stringifiers;

use Builderius\Respect\Stringifier\Quoters\CodeQuoter;
use Builderius\Respect\Stringifier\Quoters\StringQuoter;
use Builderius\Respect\Stringifier\Stringifier;
/**
 * Converts a value into a string using the defined Stringifiers.
 *
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
final class ClusterStringifier implements \Builderius\Respect\Stringifier\Stringifier
{
    /**
     * @var Stringifier[]
     */
    private $stringifiers;
    /**
     * Initializes the stringifier.
     *
     * @param Stringifier[] ...$stringifiers
     */
    public function __construct(\Builderius\Respect\Stringifier\Stringifier ...$stringifiers)
    {
        $this->setStringifiers($stringifiers);
    }
    /**
     * Create a default instance of the class.
     *
     * This instance includes all possible stringifiers.
     *
     * @return ClusterStringifier
     */
    public static function createDefault() : self
    {
        $quoter = new \Builderius\Respect\Stringifier\Quoters\CodeQuoter();
        $stringifier = new self();
        $stringifier->setStringifiers([new \Builderius\Respect\Stringifier\Stringifiers\TraversableStringifier($stringifier, $quoter), new \Builderius\Respect\Stringifier\Stringifiers\DateTimeStringifier($stringifier, $quoter, 'c'), new \Builderius\Respect\Stringifier\Stringifiers\ThrowableStringifier($stringifier, $quoter), new \Builderius\Respect\Stringifier\Stringifiers\StringableObjectStringifier($stringifier), new \Builderius\Respect\Stringifier\Stringifiers\JsonSerializableStringifier($stringifier, $quoter), new \Builderius\Respect\Stringifier\Stringifiers\ObjectStringifier($stringifier, $quoter), new \Builderius\Respect\Stringifier\Stringifiers\ArrayStringifier($stringifier, $quoter, 3, 5), new \Builderius\Respect\Stringifier\Stringifiers\InfiniteStringifier($quoter), new \Builderius\Respect\Stringifier\Stringifiers\NanStringifier($quoter), new \Builderius\Respect\Stringifier\Stringifiers\ResourceStringifier($quoter), new \Builderius\Respect\Stringifier\Stringifiers\BoolStringifier($quoter), new \Builderius\Respect\Stringifier\Stringifiers\NullStringifier($quoter), new \Builderius\Respect\Stringifier\Stringifiers\JsonParsableStringifier()]);
        return $stringifier;
    }
    /**
     * Set stringifiers.
     *
     * @param array $stringifiers
     *
     * @return void
     */
    public function setStringifiers(array $stringifiers) : void
    {
        $this->stringifiers = [];
        foreach ($stringifiers as $stringifier) {
            $this->addStringifier($stringifier);
        }
    }
    /**
     * Add a stringifier to the chain
     *
     * @param Stringifier $stringifier
     *
     * @return void
     */
    public function addStringifier(\Builderius\Respect\Stringifier\Stringifier $stringifier) : void
    {
        $this->stringifiers[] = $stringifier;
    }
    /**
     * {@inheritdoc}
     */
    public function stringify($value, int $depth) : ?string
    {
        foreach ($this->stringifiers as $stringifier) {
            $string = $stringifier->stringify($value, $depth);
            if (null === $string) {
                continue;
            }
            return $string;
        }
        return null;
    }
}
