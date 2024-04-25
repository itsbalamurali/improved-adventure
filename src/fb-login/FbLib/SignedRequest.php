<?php





namespace Facebook;

use Facebook\Exceptions\FacebookSDKException;

/**
 * Class SignedRequest.
 */
class SignedRequest
{
    /**
     * @var FacebookApp the FacebookApp entity
     */
    protected $app;

    /**
     * @var string the raw encrypted signed request
     */
    protected $rawSignedRequest;

    /**
     * @var array the payload from the decrypted signed request
     */
    protected $payload;

    /**
     * Instantiate a new SignedRequest entity.
     *
     * @param FacebookApp $facebookApp      the FacebookApp entity
     * @param null|string $rawSignedRequest the raw signed request
     */
    public function __construct(FacebookApp $facebookApp, $rawSignedRequest = null)
    {
        $this->app = $facebookApp;

        if (!$rawSignedRequest) {
            return;
        }

        $this->rawSignedRequest = $rawSignedRequest;

        $this->parse();
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
        return $this->get('oauth_token') || $this->get('code');
    }

    /**
     * Creates a signed request from an array of data.
     *
     * @return string
     */
    public function make(array $payload)
    {
        $payload['algorithm'] ??= 'HMAC-SHA256';
        $payload['issued_at'] ??= time();
        $encodedPayload = $this->base64UrlEncode(json_encode($payload));

        $hashedSig = $this->hashSignature($encodedPayload);
        $encodedSig = $this->base64UrlEncode($hashedSig);

        return $encodedSig.'.'.$encodedPayload;
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
    public function base64UrlDecode($input)
    {
        $urlDecodedBase64 = strtr($input, '-_', '+/');
        $this->validateBase64($urlDecodedBase64);

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
    public function base64UrlEncode($input)
    {
        return strtr(base64_encode($input), '+/', '-_');
    }

    /**
     * Validates and decodes a signed request and saves
     * the payload to an array.
     */
    protected function parse(): void
    {
        [$encodedSig, $encodedPayload] = $this->split();

        // Signature validation
        $sig = $this->decodeSignature($encodedSig);
        $hashedSig = $this->hashSignature($encodedPayload);
        $this->validateSignature($hashedSig, $sig);

        $this->payload = $this->decodePayload($encodedPayload);

        // Payload validation
        $this->validateAlgorithm();
    }

    /**
     * Splits a raw signed request into signature and payload.
     *
     * @return array
     *
     * @throws FacebookSDKException
     */
    protected function split()
    {
        if (!str_contains($this->rawSignedRequest, '.')) {
            throw new FacebookSDKException('Malformed signed request.', 606);
        }

        return explode('.', $this->rawSignedRequest, 2);
    }

    /**
     * Decodes the raw signature from a signed request.
     *
     * @param string $encodedSig
     *
     * @return string
     *
     * @throws FacebookSDKException
     */
    protected function decodeSignature($encodedSig)
    {
        $sig = $this->base64UrlDecode($encodedSig);

        if (!$sig) {
            throw new FacebookSDKException('Signed request has malformed encoded signature data.', 607);
        }

        return $sig;
    }

    /**
     * Decodes the raw payload from a signed request.
     *
     * @param string $encodedPayload
     *
     * @return array
     *
     * @throws FacebookSDKException
     */
    protected function decodePayload($encodedPayload)
    {
        $payload = $this->base64UrlDecode($encodedPayload);

        if ($payload) {
            $payload = json_decode($payload, true);
        }

        if (!\is_array($payload)) {
            throw new FacebookSDKException('Signed request has malformed encoded payload data.', 607);
        }

        return $payload;
    }

    /**
     * Validates the algorithm used in a signed request.
     *
     * @throws FacebookSDKException
     */
    protected function validateAlgorithm(): void
    {
        if ('HMAC-SHA256' !== $this->get('algorithm')) {
            throw new FacebookSDKException('Signed request is using the wrong algorithm.', 605);
        }
    }

    /**
     * Hashes the signature used in a signed request.
     *
     * @param string $encodedData
     *
     * @return string
     *
     * @throws FacebookSDKException
     */
    protected function hashSignature($encodedData)
    {
        $hashedSig = hash_hmac(
            'sha256',
            $encodedData,
            $this->app->getSecret(),
            $raw_output = true
        );

        if (!$hashedSig) {
            throw new FacebookSDKException('Unable to hash signature from encoded payload data.', 602);
        }

        return $hashedSig;
    }

    /**
     * Validates the signature used in a signed request.
     *
     * @param string $hashedSig
     * @param string $sig
     *
     * @throws FacebookSDKException
     */
    protected function validateSignature($hashedSig, $sig): void
    {
        if (hash_equals($hashedSig, $sig)) {
            return;
        }

        throw new FacebookSDKException('Signed request has an invalid signature.', 602);
    }

    /**
     * Validates a base64 string.
     *
     * @param string $input base64 value to validate
     *
     * @throws FacebookSDKException
     */
    protected function validateBase64($input): void
    {
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $input)) {
            throw new FacebookSDKException('Signed request contains malformed base64 encoding.', 608);
        }
    }
}
