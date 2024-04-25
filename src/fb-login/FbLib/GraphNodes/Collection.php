<?php





namespace Facebook\GraphNodes;

/*
 * Class Collection
 *
 * Modified version of Collection in "illuminate/support" by Taylor Otwell
 *
 * @package Facebook
 */

class Collection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * The items contained in the collection.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new collection.
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->asJson();
    }

    /**
     * Gets the value of a field from the Graph node.
     *
     * @param string $name    the field to retrieve
     * @param mixed  $default the default to return if the field doesn't exist
     *
     * @return mixed
     */
    public function getField($name, $default = null)
    {
        if (isset($this->items[$name])) {
            return $this->items[$name];
        }

        return $default;
    }

    /**
     * Gets the value of the named property for this graph object.
     *
     * @param string $name    the property to retrieve
     * @param mixed  $default the default to return if the property doesn't exist
     *
     * @return mixed
     *
     * @deprecated 5.0.0 getProperty() has been renamed to getField()
     *
     * @todo v6: Remove this method
     */
    public function getProperty($name, $default = null)
    {
        return $this->getField($name, $default);
    }

    /**
     * Returns a list of all fields set on the object.
     *
     * @return array
     */
    public function getFieldNames()
    {
        return array_keys($this->items);
    }

    /**
     * Returns a list of all properties set on the object.
     *
     * @return array
     *
     * @deprecated 5.0.0 getPropertyNames() has been renamed to getFieldNames()
     *
     * @todo v6: Remove this method
     */
    public function getPropertyNames()
    {
        return $this->getFieldNames();
    }

    /**
     * Get all of the items in the collection.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function asArray()
    {
        return array_map(static fn ($value) => $value instanceof Collection ? $value->asArray() : $value, $this->items);
    }

    /**
     * Run a map over each of the items.
     *
     * @return static
     */
    public function map(\Closure $callback)
    {
        return new static(array_map($callback, $this->items, array_keys($this->items)));
    }

    /**
     * Get the collection of items as JSON.
     *
     * @param int $options
     *
     * @return string
     */
    public function asJson($options = 0)
    {
        return json_encode($this->asArray(), $options);
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count()
    {
        return \count($this->items);
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return \array_key_exists($key, $this->items);
    }

    /**
     * Get an item at a given offset.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->items[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value): void
    {
        if (null === $key) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param string $key
     */
    public function offsetUnset($key): void
    {
        unset($this->items[$key]);
    }
}
