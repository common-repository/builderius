<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig\TokenParser;

use Builderius\Twig\Parser;
/**
 * Base class for all token parsers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class AbstractTokenParser implements \Builderius\Twig\TokenParser\TokenParserInterface
{
    /**
     * @var Parser
     */
    protected $parser;
    public function setParser(\Builderius\Twig\Parser $parser) : void
    {
        $this->parser = $parser;
    }
}
