<?php





namespace Facebook\PseudoRandomString;

use Facebook\Exceptions\FacebookSDKException;

class McryptPseudoRandomStringGenerator implements PseudoRandomStringGeneratorInterface
{
    use PseudoRandomStringGeneratorTrait;

    /**
     * @const string The error message when generating the string fails.
     */
    public const ERROR_MESSAGE = 'Unable to generate a cryptographically secure pseudo-random string from mcrypt_create_iv(). ';

    /**
     * @throws FacebookSDKException
     */
    public function __construct()
    {
        if (!\function_exists('mcrypt_create_iv')) {
            throw new FacebookSDKException(
                static::ERROR_MESSAGE.
                'The function mcrypt_create_iv() does not exist.'
            );
        }
    }

    public function getPseudoRandomString($length)
    {
        $this->validateLength($length);

        $binaryString = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);

        if (false === $binaryString) {
            throw new FacebookSDKException(
                static::ERROR_MESSAGE.
                'mcrypt_create_iv() returned an error.'
            );
        }

        return $this->binToHex($binaryString, $length);
    }
}
