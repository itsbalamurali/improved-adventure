<?php





namespace Facebook\GraphNodes;

/**
 * Class GraphNode.
 */
class GraphNode extends Collection
{
    /**
     * @var array maps object key names to Graph object types
     */
    protected static $graphObjectMap = [];

    /**
     * Init this Graph object.
     */
    public function __construct(array $data = [])
    {
        parent::__construct($this->castItems($data));
    }

    /**
     * Iterates over an array and detects the types each node
     * should be cast to and returns all the items as an array.
     *
     * @TODO Add auto-casting to AccessToken entities.
     *
     * @param array $data the array to iterate over
     *
     * @return array
     */
    public function castItems(array $data)
    {
        $items = [];

        foreach ($data as $k => $v) {
            if ($this->shouldCastAsDateTime($k)
                && (is_numeric($v)
                    || $this->isIso8601DateString($v))
            ) {
                $items[$k] = $this->castToDateTime($v);
            } elseif ('birthday' === $k) {
                $items[$k] = $this->castToBirthday($v);
            } else {
                $items[$k] = $v;
            }
        }

        return $items;
    }

    /**
     * Uncasts any auto-casted datatypes.
     * Basically the reverse of castItems().
     *
     * @return array
     */
    public function uncastItems()
    {
        $items = $this->asArray();

        return array_map(static function ($v) {
            if ($v instanceof \DateTime) {
                return $v->format(\DateTime::ISO8601);
            }

            return $v;
        }, $items);
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
        return json_encode($this->uncastItems(), $options);
    }

    /**
     * Detects an ISO 8601 formatted string.
     *
     * @param string $string
     *
     * @return bool
     *
     * @see https://developers.facebook.com/docs/graph-api/using-graph-api/#readmodifiers
     * @see http://www.cl.cam.ac.uk/~mgk25/iso-time.html
     * @see http://en.wikipedia.org/wiki/ISO_8601
     */
    public function isIso8601DateString($string)
    {
        // This insane regex was yoinked from here:
        // http://www.pelagodesign.com/blog/2009/05/20/iso-8601-date-validation-that-doesnt-suck/
        // ...and I'm all like:
        // http://thecodinglove.com/post/95378251969/when-code-works-and-i-dont-know-why
        $crazyInsaneRegexThatSomehowDetectsIso8601 = '/^([\+-]?\d{4}(?!\d{2}\b))'
            .'((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?'
            .'|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d'
            .'|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])'
            .'((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d'
            .'([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/';

        return 1 === preg_match($crazyInsaneRegexThatSomehowDetectsIso8601, $string);
    }

    /**
     * Determines if a value from Graph should be cast to DateTime.
     *
     * @param string $key
     *
     * @return bool
     */
    public function shouldCastAsDateTime($key)
    {
        return \in_array($key, [
            'created_time',
            'updated_time',
            'start_time',
            'end_time',
            'backdated_time',
            'issued_at',
            'expires_at',
            'publish_time',
        ], true);
    }

    /**
     * Casts a date value from Graph to DateTime.
     *
     * @param int|string $value
     *
     * @return \DateTime
     */
    public function castToDateTime($value)
    {
        if (\is_int($value)) {
            $dt = new \DateTime();
            $dt->setTimestamp($value);
        } else {
            $dt = new \DateTime($value);
        }

        return $dt;
    }

    /**
     * Casts a birthday value from Graph to Birthday.
     *
     * @param string $value
     *
     * @return Birthday
     */
    public function castToBirthday($value)
    {
        return new Birthday($value);
    }

    /**
     * Getter for $graphObjectMap.
     *
     * @return array
     */
    public static function getObjectMap()
    {
        return static::$graphObjectMap;
    }
}
