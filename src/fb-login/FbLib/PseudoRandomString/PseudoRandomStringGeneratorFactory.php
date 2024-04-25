<?php





namespace Facebook\PseudoRandomString;

use Facebook\Exceptions\FacebookSDKException;

class PseudoRandomStringGeneratorFactory
{
    private function __construct()
    {
        // a factory constructor should never be invoked
    }

    /**
     * Pseudo random string generator creation.
     *
     * @param null|PseudoRandomStringGeneratorInterface|string $generator
     *
     * @return PseudoRandomStringGeneratorInterface
     *
     * @throws \InvalidArgumentException if the pseudo random string generator must be set to "random_bytes", "mcrypt", "openssl", or "urandom", or be an instance of Facebook\PseudoRandomString\PseudoRandomStringGeneratorInterface
     */
    public static function createPseudoRandomStringGenerator($generator)
    {
        if (!$generator) {
            return self::detectDefaultPseudoRandomStringGenerator();
        }

        if ($generator instanceof PseudoRandomStringGeneratorInterface) {
            return $generator;
        }

        if ('random_bytes' === $generator) {
            return new RandomBytesPseudoRandomStringGenerator();
        }
        if ('mcrypt' === $generator) {
            return new McryptPseudoRandomStringGenerator();
        }
        if ('openssl' === $generator) {
            return new OpenSslPseudoRandomStringGenerator();
        }
        if ('urandom' === $generator) {
            return new UrandomPseudoRandomStringGenerator();
        }

        throw new \InvalidArgumentException('The pseudo random string generator must be set to "random_bytes", "mcrypt", "openssl", or "urandom", or be an instance of Facebook\PseudoRandomString\PseudoRandomStringGeneratorInterface');
    }

    /**
     * Detects which pseudo-random string generator to use.
     *
     * @return PseudoRandomStringGeneratorInterface
     *
     * @throws FacebookSDKException if unable to detect a cryptographically secure pseudo-random string generator
     */
    private static function detectDefaultPseudoRandomStringGenerator()
    {
        // Check for PHP 7's CSPRNG first to keep mcrypt deprecation messages from appearing in PHP 7.1.
        if (\function_exists('random_bytes')) {
            return new RandomBytesPseudoRandomStringGenerator();
        }

        // Since openssl_random_pseudo_bytes() can sometimes return non-cryptographically
        // secure pseudo-random strings (in rare cases), we check for mcrypt_create_iv() next.
        if (\function_exists('mcrypt_create_iv')) {
            return new McryptPseudoRandomStringGenerator();
        }

        if (\function_exists('openssl_random_pseudo_bytes')) {
            return new OpenSslPseudoRandomStringGenerator();
        }

        if (!\ini_get('open_basedir') && is_readable('/dev/urandom')) {
            return new UrandomPseudoRandomStringGenerator();
        }

        throw new FacebookSDKException('Unable to detect a cryptographically secure pseudo-random string generator.');
    }
}
