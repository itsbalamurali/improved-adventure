<?php





namespace Facebook\Url;

/**
 * Class FacebookUrlManipulator.
 */
class FacebookUrlManipulator
{
    /**
     * Remove params from a URL.
     *
     * @param string $url            the URL to filter
     * @param array  $paramsToFilter the params to filter from the URL
     *
     * @return string the URL with the params removed
     */
    public static function removeParamsFromUrl($url, array $paramsToFilter)
    {
        $parts = parse_url($url);

        $query = '';
        if (isset($parts['query'])) {
            $params = [];
            parse_str($parts['query'], $params);

            // Remove query params
            foreach ($paramsToFilter as $paramName) {
                unset($params[$paramName]);
            }

            if (\count($params) > 0) {
                $query = '?'.http_build_query($params, null, '&');
            }
        }

        $scheme = isset($parts['scheme']) ? $parts['scheme'].'://' : '';
        $host = $parts['host'] ?? '';
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';
        $path = $parts['path'] ?? '';
        $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

        return $scheme.$host.$port.$path.$query.$fragment;
    }

    /**
     * Gracefully appends params to the URL.
     *
     * @param string $url       the URL that will receive the params
     * @param array  $newParams the params to append to the URL
     *
     * @return string
     */
    public static function appendParamsToUrl($url, array $newParams = [])
    {
        if (empty($newParams)) {
            return $url;
        }

        if (!str_contains($url, '?')) {
            return $url.'?'.http_build_query($newParams, null, '&');
        }

        [$path, $query] = explode('?', $url, 2);
        $existingParams = [];
        parse_str($query, $existingParams);

        // Favor params from the original URL over $newParams
        $newParams = array_merge($newParams, $existingParams);

        // Sort for a predicable order
        ksort($newParams);

        return $path.'?'.http_build_query($newParams, null, '&');
    }

    /**
     * Returns the params from a URL in the form of an array.
     *
     * @param string $url the URL to parse the params from
     *
     * @return array
     */
    public static function getParamsAsArray($url)
    {
        $query = parse_url($url, PHP_URL_QUERY);
        if (!$query) {
            return [];
        }
        $params = [];
        parse_str($query, $params);

        return $params;
    }

    /**
     * Adds the params of the first URL to the second URL.
     *
     * Any params that already exist in the second URL will go untouched.
     *
     * @param string $urlToStealFrom the URL harvest the params from
     * @param string $urlToAddTo     the URL that will receive the new params
     *
     * @return string the $urlToAddTo with any new params from $urlToStealFrom
     */
    public static function mergeUrlParams($urlToStealFrom, $urlToAddTo)
    {
        $newParams = static::getParamsAsArray($urlToStealFrom);
        // Nothing new to add, return as-is
        if (!$newParams) {
            return $urlToAddTo;
        }

        return static::appendParamsToUrl($urlToAddTo, $newParams);
    }

    /**
     * Check for a "/" prefix and prepend it if not exists.
     *
     * @param null|string $string
     *
     * @return null|string
     */
    public static function forceSlashPrefix($string)
    {
        if (!$string) {
            return $string;
        }

        return str_starts_with($string, '/') ? $string : '/'.$string;
    }

    /**
     * Trims off the hostname and Graph version from a URL.
     *
     * @param string $urlToTrim the URL the needs the surgery
     *
     * @return string the $urlToTrim with the hostname and Graph version removed
     */
    public static function baseGraphUrlEndpoint($urlToTrim)
    {
        return '/'.preg_replace('/^https:\/\/.+\.facebook\.com(\/v.+?)?\//', '', $urlToTrim);
    }
}
