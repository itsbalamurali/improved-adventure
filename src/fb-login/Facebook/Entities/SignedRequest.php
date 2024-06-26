<?php





namespace Facebook\Entities;

use Facebook\FacebookSDKException;
use Facebook\FacebookSession;

/**
 * Class SignedRequest.
 */
class SignedRequest
{
    /**
     * @var string
     */
    public $rawSignedRequest;

    /**
     * @var array
     */
    public $payload;

    /**
     * Instantiate a new SignedRequest entity.
     *
     * @param null|string $rawSignedRequest the raw signed request
     * @param null|string $state            random string to prevent CSRF
     * @param null|string $appSecret
     */
    public function __construct($rawSignedRequest = null, $state = null, $appSecret = null)
    {
        if (!$rawSignedRequest) {
            return;
        }

        $this->rawSignedRequest = $rawSignedRequest;
        $this->payload = static::parse($rawSignedRequest, $state, $appSecret);
    }

    /**
     * Returns the raw signed request data.
     *
     * @return null|string
     */
    public function getRawSignedRequest()
    {
        return $this->rawSignedRequest;
    }

    /**
     * Returns the parsed signed request data.
     *
     * @return null|array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Returns a property from the signed request data if available.
     *
     * @param string     $key
     * @param null|mixed $default
     *
     * @return null|mixed
     */
    public function get($key, $default = null)
    {
        if (isset($this->payload[$key])) {
            return $this->payload[$key];
        }

        return $default;
    }

    /**
     * Returns user_id from signed request data if available.
     *
     * @return null|string
     */
    public function getUserId()
    {
        return $this->get('user_id');
    }

    /**
     * Checks for OAuth data in the payload.
     *
     * @return bool
     */
    public function hasOAuthData()
    {
        return isset($this->payload['oauth_token']) || isset($this->payload['code']);
    }

    /**
     * Creates a signed request from an array of data.
     *
     * @param null|string $appSecret
     *
     * @return string
     */
    public static function make(array $payload, $appSecret = null)
    {
        $payload['algorithm'] = 'HMAC-SHA256';
        $payload['issued_at'] = time();
        $encodedPayload = static::base64UrlEncode(json_encode($payload));

        $hashedSig = static::hashSignature($encodedPayload, $appSecret);
        $encodedSig = static::base64UrlEncode($hashedSig);

        return $encodedSig.'.'.$encodedPayload;
    }

    /**
     * Validates and decodes a signed request and returns
     * the payload as an array.
     *
     * @param string      $signedRequest
     * @param null|string $state
     * @param null|string $appSecret
     *
     * @return array
     */
    public static function parse($signedRequest, $state = null, $appSecret = null)
    {
        [$encodedSig, $encodedPayload] = static::split($signedRequest);

        // Signature validation
        $sig = static::decodeSignature($encodedSig);
        $hashedSig = static::hashSignature($encodedPayload, $appSecret);
        static::validateSignature($hashedSig, $sig);

        // Payload validation
        $data = static::decodePayload($encodedPayload);
        static::validateAlgorithm($data);
        if ($state) {
            static::validateCsrf($data, $state);
        }

        return $data;
    }

    /**
     * Validates the format of a signed request.
     *
     * @param string $signedRequest
     *
     * @throws FacebookSDKException
     */
    public static function validateFormat($signedRequest): void
    {
        if (str_contains($signedRequest, '.')) {
            return;
        }

        throw new FacebookSDKException(
            'Malformed signed request.',
            606
        );
    }

    /**
     * Decodes a raw valid signed request.
     *
     * @param string $signedRequest
     *
     * @returns array
     */
    public static function split($signedRequest)
    {
        static::validateFormat($signedRequest);

        return explode('.', $signedRequest, 2);
    }

    /**
     * Decodes the raw signature from a signed request.
     *
     * @param string $encodedSig
     *
     * @returns string
     *
     * @throws FacebookSDKException
     */
    public static function decodeSignature($encodedSig)
    {
        $sig = static::base64UrlDecode($encodedSig);

        if ($sig) {
            return $sig;
        }

        throw new FacebookSDKException(
            'Signed request has malformed encoded signature data.',
            607
        );
    }

    /**
     * Decodes the raw payload from a signed request.
     *
     * @param string $encodedPayload
     *
     * @returns array
     *
     * @throws FacebookSDKException
     */
    public static function decodePayload($encodedPayload)
    {
        $payload = static::base64UrlDecode($encodedPayload);

        if ($payload) {
            $payload = json_decode($payload, true);
        }

        if (\is_array($payload)) {
            return $payload;
        }

        throw new FacebookSDKException(
            'Signed request has malformed encoded payload data.',
            607
        );
    }

    /**
     * Validates the algorithm used in a signed request.
     *
     * @throws FacebookSDKException
     */
    public static function validateAlgorithm(array $data): void
    {
        if (isset($data['algorithm']) && 'HMAC-SHA256' === $data['algorithm']) {
            return;
        }

        throw new FacebookSDKException(
            'Signed request is using the wrong algorithm.',
            605
        );
    }

    /**
     * Hashes the signature used in a signed request.
     *
     * @param string      $encodedData
     * @param null|string $appSecret
     *
     * @return string
     *
     * @throws FacebookSDKException
     */
    public static function hashSignature($encodedData, $appSecret = null)
    {
        $hashedSig = hash_hmac(
            'sha256',
            $encodedData,
            FacebookSession::_getTargetAppSecret($appSecret),
            $raw_output = true
        );

        if ($hashedSig) {
            return $hashedSig;
        }

        throw new FacebookSDKException(
            'Unable to hash signature from encoded payload data.',
            602
        );
    }

    /**
     * Validates the signature used in a signed request.
     *
     * @param string $hashedSig
     * @param string $sig
     *
     * @throws FacebookSDKException
     */
    public static function validateSignature($hashedSig, $sig): void
    {
        if (mb_strlen($hashedSig) === mb_strlen($sig)) {
            $validate = 0;
            for ($i = 0; $i < mb_strlen($sig); ++$i) {
                $validate |= \ord($hashedSig[$i]) ^ \ord($sig[$i]);
            }
            if (0 === $validate) {
                return;
            }
        }

        throw new FacebookSDKException(
            'Signed request has an invalid signature.',
            602
        );
    }

    /**
     * Validates a signed request against CSRF.
     *
     * @param string $state
     *
     * @throws FacebookSDKException
     */
    public static function validateCsrf(array $data, $state): void
    {
        if (isset($data['state']) && $data['state'] === $state) {
            return;
        }

        throw new FacebookSDKException(
            'Signed request did not pass CSRF validation.',
            604
        );
    }

    /**
     * Base64 decoding which replaces characters:
     *   + instead of -
     *   / instead of _
     *
     * @see http://en.wikipedia.org/wiki/Base64#URL_applications
     *
     * @param string $input base64 url encoded input
     *
     * @return string decoded string
     */
    public static function base64UrlDecode($input)
    {
        $urlDecodedBase64 = strtr($input, '-_', '+/');
        static::validateBase64($urlDecodedBase64);

        return base64_decode($urlDecodedBase64, true);
    }

    /**
     * Base64 encoding which replaces characters:
     *   + instead of -
     *   / instead of _
     *
     * @see http://en.wikipedia.org/wiki/Base64#URL_applications
     *
     * @param string $input string to encode
     *
     * @return string base64 url encoded input
     */
    public static function base64UrlEncode($input)
    {
        return strtr(base64_encode($input), '+/', '-_');
    }

    /**
     * Validates a base64 string.
     *
     * @param string $input base64 value to validate
     *
     * @throws FacebookSDKException
     */
    public static function validateBase64($input): void
    {
        $pattern = '/^[a-zA-Z0-9\/\r\n+]*={0,2}$/';
        if (preg_match($pattern, $input)) {
            return;
        }

        throw new FacebookSDKException(
            'Signed request contains malformed base64 encoding.',
            608
        );
    }
}
