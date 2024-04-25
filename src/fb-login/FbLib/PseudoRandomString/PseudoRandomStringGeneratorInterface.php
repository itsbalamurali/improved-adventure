<?php





namespace Facebook\PseudoRandomString;

use Facebook\Exceptions\FacebookSDKException;

/**
 * Interface.
 */
interface PseudoRandomStringGeneratorInterface
{
    /**
     * Get a cryptographically secure pseudo-random string of arbitrary length.
     *
     * @see http://sockpuppet.org/blog/2014/02/25/safely-generate-random-numbers/
     *
     * @param int $length the length of the string to return
     *
     * @return string
     *
     * @throws FacebookSDKException|\InvalidArgumentException
     */
    public function getPseudoRandomString($length);
}
