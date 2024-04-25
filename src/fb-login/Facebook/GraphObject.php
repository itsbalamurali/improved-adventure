<?php





namespace Facebook;

/**
 * Class GraphObject.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @author David Poll <depoll@fb.com>
 */
class GraphObject
{
    /**
     * @var array - Holds the raw associative data for this object
     */
    protected $backingData;

    /**
     * Creates a GraphObject using the data provided.
     *
     * @param array $raw
     */
    public function __construct($raw)
    {
        if ($raw instanceof \stdClass) {
            $raw = get_object_vars($raw);
        }

        $this->backingData = $raw;

        if (isset($this->backingData['data']) && 1 === \count($this->backingData)) {
            if ($this->backingData['data'] instanceof \stdClass) {
                $this->backingData = get_object_vars($this->backingData['data']);
            } else {
                $this->backingData = $this->backingData['data'];
            }
        }
    }

    /**
     * cast - Return a new instance of a FacebookGraphObject subclass for this
     *   objects underlying data.
     *
     * @param string $type The GraphObject subclass to cast to
     *
     * @return GraphObject
     *
     * @throws FacebookSDKException
     */
    public function cast($type)
    {
        if ($this instanceof $type) {
            return $this;
        }
        if (is_subclass_of($type, self::className())) {
            return new $type($this->backingData);
        }

        throw new FacebookSDKException(
            'Cannot cast to an object that is not a GraphObject subclass',
            620
        );
    }

    /**
     * asArray - Return a key-value associative array for the given graph object.
     *
     * @return array
     */
    public function asArray()
    {
        return $this->backingData;
    }

    /**
     * getProperty - Gets the value of the named property for this graph object,
     *   cast to the appropriate subclass type if provided.
     *
     * @param string $name The property to retrieve
     * @param string $type The subclass of GraphObject, optionally
     *
     * @return mixed
     */
    public function getProperty($name, $type = 'Facebook\GraphObject')
    {
        if (isset($this->backingData[$name])) {
            $value = $this->backingData[$name];
            if (\is_scalar($value)) {
                return $value;
            }

            return (new self($value))->cast($type);
        }

        return null;
    }

    /**
     * getPropertyAsArray - Get the list value of a named property for this graph
     *   object, where each item has been cast to the appropriate subclass type
     *   if provided.
     *
     * Calling this for a property that is not an array, the behavior
     *   is undefined, so don’t do this.
     *
     * @param string $name The property to retrieve
     * @param string $type The subclass of GraphObject, optionally
     *
     * @return array
     */
    public function getPropertyAsArray($name, $type = 'Facebook\GraphObject')
    {
        $target = [];
        if (isset($this->backingData[$name]['data'])) {
            $target = $this->backingData[$name]['data'];
        } elseif (isset($this->backingData[$name])
          && !\is_scalar($this->backingData[$name])) {
            $target = $this->backingData[$name];
        }
        $out = [];
        foreach ($target as $key => $value) {
            if (\is_scalar($value)) {
                $out[$key] = $value;
            } else {
                $out[$key] = (new self($value))->cast($type);
            }
        }

        return $out;
    }

    /**
     * getPropertyNames - Returns a list of all properties set on the object.
     *
     * @return array
     */
    public function getPropertyNames()
    {
        return array_keys($this->backingData);
    }

    /**
     * Returns the string class name of the GraphObject or subclass.
     *
     * @return string
     */
    public static function className()
    {
        return static::class;
    }
}
