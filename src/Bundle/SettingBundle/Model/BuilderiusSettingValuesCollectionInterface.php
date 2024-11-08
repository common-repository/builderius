<?php

namespace Builderius\Bundle\SettingBundle\Model;

use ArrayAccess;
use Countable;
use IteratorAggregate;

interface BuilderiusSettingValuesCollectionInterface extends Countable, IteratorAggregate, ArrayAccess
{
    /**
     * Adds an element at the end of the collection.
     *
     * @param BuilderiusSettingValueInterface $element The element to add.
     *
     * @return bool Always TRUE.
     */
    public function add(BuilderiusSettingValueInterface $element);

    /**
     * Clears the collection, removing all elements.
     *
     * @return void
     */
    public function clear();

    /**
     * Checks whether an element is contained in the collection.
     * This is an O(n) operation, where n is the size of the collection.
     *
     * @param BuilderiusSettingValueInterface $element The element to search for.
     *
     * @return bool TRUE if the collection contains the element, FALSE otherwise.
     */
    public function contains(BuilderiusSettingValueInterface $element);

    /**
     * Checks whether the collection is empty (contains no elements).
     *
     * @return bool TRUE if the collection is empty, FALSE otherwise.
     */
    public function isEmpty();

    /**
     * Removes the element at the specified index from the collection.
     *
     * @param string|int $key The kex/index of the element to remove.
     *
     * @return BuilderiusSettingValueInterface The removed element or NULL, if the collection did not contain the element.
     */
    public function remove($key);

    /**
     * Removes the specified element from the collection, if it is found.
     *
     * @param BuilderiusSettingValueInterface $element The element to remove.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeElement(BuilderiusSettingValueInterface $element);

    /**
     * Checks whether the collection contains an element with the specified key/index.
     *
     * @param string|int $key The key/index to check for.
     *
     * @return bool TRUE if the collection contains an element with the specified key/index,
     *              FALSE otherwise.
     */
    public function containsKey($key);

    /**
     * Gets the element at the specified key/index.
     *
     * @param string|int $key The key/index of the element to retrieve.
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Gets all keys/indices of the collection.
     *
     * @return array The keys/indices of the collection, in the order of the corresponding
     *               elements in the collection.
     */
    public function getKeys();

    /**
     * Gets all values of the collection.
     *
     * @return array The values of all elements in the collection, in the order they
     *               appear in the collection.
     */
    public function getValues();

    /**
     * Sets an element in the collection at the specified key/index.
     *
     * @param string|int $key   The key/index of the element to set.
     * @param BuilderiusSettingValueInterface $value The element to set.
     *
     * @return void
     */
    public function set($key, BuilderiusSettingValueInterface $value);

    /**
     * Gets a native PHP array representation of the collection.
     *
     * @return array
     */
    public function toArray();

    /**
     * Sets the internal iterator to the first element in the collection and returns this element.
     *
     * @return BuilderiusSettingValueInterface
     */
    public function first();

    /**
     * Sets the internal iterator to the last element in the collection and returns this element.
     *
     * @return BuilderiusSettingValueInterface
     */
    public function last();

    /**
     * Gets the key/index of the element at the current iterator position.
     *
     * @return int|string
     */
    public function key();

    /**
     * Gets the element of the collection at the current iterator position.
     *
     * @return BuilderiusSettingValueInterface
     */
    public function current();

    /**
     * Moves the internal iterator position to the next element and returns this element.
     *
     * @return BuilderiusSettingValueInterface
     */
    public function next();

    /**
     * Gets the index/key of a given element. The comparison of two elements is strict,
     * that means not only the value but also the type must match.
     * For objects this means reference equality.
     *
     * @param BuilderiusSettingValueInterface $element The element to search for.
     *
     * @return int|string|bool The key/index of the element or FALSE if the element was not found.
     */
    public function indexOf(BuilderiusSettingValueInterface $element);

    /**
     * Extracts a slice of $length elements starting at position $offset from the Collection.
     *
     * If $length is null it returns all elements from $offset to the end of the Collection.
     * Keys have to be preserved by this method. Calling this method will only return the
     * selected slice and NOT change the elements contained in the collection slice is called on.
     *
     * @param int      $offset The offset to start from.
     * @param int|null $length The maximum number of elements to return, or null for no limit.
     *
     * @return array
     */
    public function slice($offset, $length = null);
}
