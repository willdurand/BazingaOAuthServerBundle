<?php

namespace Bazinga\OAuthServerBundle\Service;

use Bazinga\OAuthServerBundle\Model\ConsumerInterface;
use Bazinga\OAuthServerBundle\Model\TokenInterface;
use Bazinga\OAuthServerBundle\Model\Provider\ConsumerProviderInterface;
use Bazinga\OAuthServerBundle\Model\Provider\NonceProviderInterface;
use Bazinga\OAuthServerBundle\Model\Provider\TokenProviderInterface;
use Bazinga\OAuthServerBundle\Service\Signature\OAuthSignatureInterface;

/**
 * OAuth version 1.0.
 *
 * If you want to create your own OAuthServerService implementation,
 * this class provides some useful tools.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
abstract class OAuthAbstractServerService implements OAuthServerServiceInterface
{
    /**
     * OAuth version.
     */
    const OAUTH_VERSION                             = '1.0';

    /**
     * The default interval time to use when you check the timestamp
     * of a request token.
     */
    const DEFAULT_REQUEST_TOKEN_INTERVAL            = 600;

    /**
     * The default duration in seconds of the request token lifetime.
     */
    const DEFAULT_REQUEST_TOKEN_LIFETIME            = 3600;

    /**
     * The default duration in seconds of the access token lifetime.
     */
    const DEFAULT_ACCESS_TOKEN_LIFETIME             = 3600;

    /**
     * The default duration in seconds of the authorization code lifetime.
     */
    const DEFAULT_AUTH_CODE_LIFETIME                = 30;

    /**
     * The default duration in seconds of the refresh token lifetime.
     */
    const DEFAULT_REFRESH_TOKEN_LIFETIME            = 1209600;

    /**
     * The version of OAuth used is not supported.
     * @see http://developer.yahoo.com/oauth/guide/oauth-errors.html
     */
    const ERROR_VERSION_REJECTED                    = 'version_rejected';

    /**
     * The request has a missing parameter. If all parameters are present,
     * a common reason for this error is typos in the Authorization header.
     * Check for spelling errors, misplaced single/double quotes. etc.
     * Remember that each OAuth Protocol parameter value must to be enclosed
     * in double quotes.
     */
    const ERROR_PARAMETER_ABSENT                    = 'parameter_absent';

    /**
     * The timestamp provided is invalid (either it doesn't have the right
     * format, or it's out of the acceptable window).
     */
    const ERROR_TIMESTAMP_REFUSED                   = 'timestamp_refused';

    /**
     * The nonce received is not acceptable.
     */
    const ERROR_NONCE_USED                          = 'nonce_used';

    /**
     * The signature method used is unsupported.
     */
    const ERROR_SIGNATURE_METHOD_REJECTED           = 'signature_method_rejected';

    /**
     * The signature provided does not match the one calculated by the service.
     */
    const ERROR_SIGNATURE_INVALID                   = 'signature_invalid';

    /**
     * The consumer key provided is unsupported.
     */
    const ERROR_CONSUMER_KEY_UNKNOWN                = 'consumer_key_unknown';

    /**
     * The access token provided is valid, but has expired.
     */
    const ERROR_TOKEN_EXPIRED                       = 'token_expired';

    /**
     * The token provided does not have the right format.
     */
    const ERROR_TOKEN_REJECTED                      = 'token_rejected';

    /**
     * The access token does not have the correct access scopes.
     */
    const ERROR_ADDITIONAL_AUTHORIZATION_REQUIRED   = 'additional_authorization_required';

    /**
     * The access session handle (ASH) has expired or is invalid.
     * This error usually occurs when refreshing the Access Token.
     */
    const ERROR_PERMISSION_DENIED                   = 'permission_denied';

    /**
     * @var ConsumerProviderInterface
     */
    protected $consumerProvider;

    /**
     * @var TokenProviderInterface
     */
    protected $tokenProvider;

    /**
     * @var NonceProviderInterface
     */
    protected $nonceProvider;

    /**
     * An array of signature services that implement OAuthSignatureInterface.
     * @var array
     */
    protected $signatureServices;

    /**
     * An array of required parameters names for the request token process.
     * @var array
     */
    protected $requiredParamsForRequestToken;

    /**
     * An array of required parameters names for the access token process.
     * @var array
     */
    protected $requiredParamsForAccessToken;

    /**
     * An array of required parameters names for the access resource process.
     * @var array
     */
    protected $requiredParamsForValidRequest;

    /**
     * Constructor.
     *
     * @param ConsumerProviderInterface $consumerProvider The consumer provider.
     * @param TokenProviderInterface    $tokenProvider    The consumer provider.
     */
    public function __construct(ConsumerProviderInterface $consumerProvider, TokenProviderInterface $tokenProvider, NonceProviderInterface $nonceProvider)
    {
        $this->consumerProvider  = $consumerProvider;
        $this->tokenProvider     = $tokenProvider;
        $this->nonceProvider     = $nonceProvider;

        $this->signatureServices = array();

        $this->requiredParamsForRequestToken = array(
            'oauth_consumer_key',
            'oauth_signature_method',
            'oauth_signature',
            'oauth_timestamp',
            'oauth_nonce',
        );
        $this->requiredParamsForAccessToken = array(
            'oauth_consumer_key',
            'oauth_signature_method',
            'oauth_nonce',
            'oauth_signature',
            'oauth_timestamp',
            'oauth_verifier',
            'oauth_token'
        );
        $this->requiredParamsForValidRequest = array(
            'oauth_consumer_key',
            'oauth_nonce',
            'oauth_signature_method',
            'oauth_timestamp',
            'oauth_token',
            'oauth_signature'
        );
    }

    /**
     * {@inheritdoc}
     * @return \Bazinga\OAuthServerBundle\Model\Provider\TokenProviderInterface
     */
    public function getTokenProvider()
    {
        return $this->tokenProvider;
    }

    /**
     * {@inheritdoc}
     * @return \Bazinga\OAuthServerBundle\Model\Provider\ConsumerProviderInterface
     */
    public function getConsumerProvider()
    {
        return $this->consumerProvider;
    }

    /**
     * Registers a signature service.
     *
     * @param OAuthSignatureInterface $signatureService The signature service to register.
     */
    public function addSignatureService(OAuthSignatureInterface $signatureService)
    {
        $this->signatureServices[strtolower($signatureService->getName())] = $signatureService;
    }

    /**
     * Returns a signature service identified by its name.
     *
     * @param  string                  $signatureServiceName A signature service name.
     * @return OAuthSignatureInterface The signature service or <code>null</code> if not found.
     */
    protected function getSignatureService($signatureServiceName)
    {
        if (! array_key_exists(strtolower($signatureServiceName), $this->signatureServices)) {
            return null;
        }

        return $this->signatureServices[strtolower($signatureServiceName)];
    }

    /**
     * Returns the request token lifetime.
     *
     * @return int
     */
    public function getRequestTokenLifetime()
    {
        return self::DEFAULT_REQUEST_TOKEN_LIFETIME;
    }

    /**
     * Returns the access token lifetime.
     *
     * @return int
     */
    public function getAccessTokenLifetime()
    {
        return self::DEFAULT_ACCESS_TOKEN_LIFETIME;
    }

    /**
     * Returns the request token interval time.
     *
     * @return int
     */
    public function getRequestTokenInterval()
    {
        return self::DEFAULT_REQUEST_TOKEN_INTERVAL;
    }

    /**
     * Check if the provided timestamp is valid or not.
     *
     * @param  string  $oauthTimestamp A timestamp string.
     * @return boolean <code>true</code> if the timestamp is valid,
     *                  <code>false</code> otherwise.
     */
    protected function checkTimestamp($oauthTimestamp)
    {
        $maxTimestamp = time() + $this->getRequestTokenInterval();
        $minTimestamp = time() - $this->getRequestTokenInterval();

        return ($oauthTimestamp > $minTimestamp && $oauthTimestamp < $maxTimestamp);
    }

    /**
     * Check if the provided version is valid or not.
     *
     * @param  string  $oauthVersion a version.
     * @return boolean <code>true</code> if the version is valid,
     *                  <code>false</code> otherwise.
     */
    protected function checkVersion($oauthVersion)
    {
        return (self::OAUTH_VERSION === $oauthVersion);
    }

    /**
     * Normalize request parameters.
     *
     * @see http://oauth.net/core/1.O/#rfc.section.9.1.1
     *
     * @param  array $requestParameters An array of request parameters to normalize.
     * @param string $parentKey
     * @return string
     */
    protected function normalizeRequestParameters($requestParameters, $parentKey = '')
    {
        if (empty($requestParameters)) {
            return '';
        }

        $normalizedParameters = array();
        foreach ($requestParameters as $key => $value) {
            if ('oauth_signature' === $key || empty($value)) {
                continue;
            }

            if ($parentKey !== '') {
                // Multidimensional array; using foo=bar&foo=baz rather than foo[bar]=baz&foo[baz]=bar
                $key = $parentKey;
            }

            if (is_array($value)) {
                $normalizedParameters[] = $this->normalizeRequestParameters($value, $key);
            } else {
                $normalizedParameters[] = rawurlencode($key) . '=' . rawurlencode($value);
            }
        }

        $normalizedParameters = array_filter($normalizedParameters);
        sort($normalizedParameters, SORT_STRING);

        return implode('&', $normalizedParameters);
    }

    /**
     * Concatenate request elements.
     * @see http://oauth.net/core/1.O/#rfc.section.9.1.3
     *
     * @param  OAuthSignatureInterface $signatureService A signature service.
     * @param  string                  $requestMethod    The request method (POST, GET, ...)
     * @param  string                  $requestUrl       The request url (see http://oauth.net/core/1.0/#rfc.section.9.1.2)
     * @return string
     */
    protected function getSignatureBaseString(OAuthSignatureInterface $signatureService, $requestMethod, $requestUrl, $normalizedParameters)
    {
        return sprintf('%s&%s&%s',
            $signatureService->urlencode($requestMethod),
            $signatureService->urlencode($requestUrl),
            $signatureService->urlencode($normalizedParameters)
        );
    }

    /**
     * Calculate the signature and compare it to the given signature.
     *
     * @param  ConsumerInterface $consumer          A consumer.
     * @param  TokenInterface    $token             A token.
     * @param  array             $requestParameters An array of request parameters.
     * @param  string            $requestMethod     The request method.
     * @param  string            $requestUrl        The request UI
     * @return boolean           <code>true</code> if the provided signature is correct,
     *                   <code>false</code> otherwise.
     */
    protected function approveSignature(ConsumerInterface $consumer, TokenInterface $token = null, $requestParameters, $requestMethod, $requestUrl)
    {
        $signatureService = $this->getSignatureService($requestParameters['oauth_signature_method']);

        $baseString = $this->getSignatureBaseString($signatureService, $requestMethod, $requestUrl, $this->normalizeRequestParameters($requestParameters));

        $secretToken = (null !== $token) ? $token->getSecret() : '';
        $calculatedSignature = $signatureService->sign($baseString, $consumer->getConsumerSecret(), $secretToken);

        return ($calculatedSignature === $requestParameters['oauth_signature']);
    }

    /**
     * Returns a string for a given token according to the RFC.
     *
     * @param  TokenInterface $token    A token.
     * @param  array          $extras   An array of extra parameters to add in the response.
     * @param  integer        $lifetime The token lifetime.
     * @return string
     */
    protected function sendToken(TokenInterface $token, $lifetime = 3600, array $extras = array())
    {
        $returnedParameters = array();

        $returnedParameters['oauth_token']        = $token->getToken();
        $returnedParameters['oauth_token_secret'] = $token->getSecret();
        $returnedParameters['oauth_expires_in']   = (null !== $token->getExpiresIn()) ? $token->getExpiresIn() : $lifetime;

        foreach ($extras as $key => $value) {
            $returnedParameters[$key] = $value;
        }

        return http_build_query($returnedParameters);
    }
}
