<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\PropertyAccess;

/**
 * Traverses a property path and provides additional methods to find out
 * information about the current element.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class PropertyPathIterator extends \ArrayIterator implements \Builderius\Symfony\Component\PropertyAccess\PropertyPathIteratorInterface
{
    protected $path;
    public function __construct(\Builderius\Symfony\Component\PropertyAccess\PropertyPathInterface $path)
    {
        parent::__construct($path->getElements());
        $this->path = $path;
    }
    /**
     * {@inheritdoc}
     */
    public function isIndex()
    {
        return $this->path->isIndex($this->key());
    }
    /**
     * {@inheritdoc}
     */
    public function isProperty()
    {
        return $this->path->isProperty($this->key());
    }
}