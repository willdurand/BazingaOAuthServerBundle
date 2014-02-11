<?php

namespace Bazinga\OAuthServerBundle\Service;

/**
 * Describes how an OAuth server works.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
interface OAuthServerServiceInterface
{
    /**
     * Returns the token provider in use.
     *
     * @return \Bazinga\OAuthServerBundle\Model\Provider\TokenProviderInterface
     */
    public function getTokenProvider();

    /**
     * Returns the consumer provider in use.
     *
     * @return \Bazinga\OAuthServerBundle\Model\Provider\ConsumerProviderInterface
     */
    public function getConsumerProvider();

    /**
     * Obtaining a Request Token
     *
     * @see http://tools.ietf.org/html/rfc5849#section-2.1
     * @see http://oauth.net/core/1.0a/#auth_step1
     *
     * @param  array  $requestParameters An array of request parameters.
     * @param  string $requestMethod     The request method (GET, POST, ...)
     * @param  string $requestUrl        The request URL (http://oauth.net/core/1.0/#rfc.section.9.1.2)
     * @return string An unauthorized Request Token as a string.
     *
     * Example: oauth_token=hh5s93j4hdidpola&oauth_token_secret=hdhd0244k9j7ao03&oauth_callback_confirmed=true
     */
    public function requestToken($requestParameters, $requestMethod, $requestUrl);

    /**
     * Requesting User Authorization
     *
     * @see http://tools.ietf.org/html/rfc5849#section-2.2
     * @see http://oauth.net/core/1.0a/#auth_step2
     *
     * @param  string $oauthToken    An OAuth request token.
     * @param  string $oauthCallback A callback URL.
     * @return string An URL to call if the callback is not 'oob', otherwise
     *                              a string with `oauth_token` and `oauth_verifier` parameters.
     * Example:
     *  http://printer.example.com/request_token_ready?oauth_token=hh5s93j4hdidpola&oauth_verifier=hfdp7dh39dks9884
     * or
     *  oauth_token=hh5s93j4hdidpola&oauth_verifier=hfdp7dh39dks9884
     */
    public function authorize($oauthToken, $oauthCallback = null);

    /**
     * Obtaining an Access Token
     *
     * @see http://tools.ietf.org/html/rfc5849#section-2.3
     * @see http://oauth.net/core/1.0a/#auth_step3
     *
     * @param  array  $requestParameters An array of request parameters.
     * @param  string $requestMethod     The request method (GET, POST, ...)
     * @param  string $requestUrl        The request URL (http://oauth.net/core/1.0/#rfc.section.9.1.2)
     * @return string A set of credentials as a string.
     *                                  Example: oauth_token=nnch734d00sl2jdk&oauth_token_secret=pfkkdhi9sl3r4s00
     */
    public function accessToken($requestParameters, $requestMethod, $requestUrl);

    /**
     * Accessing Protected Resources
     *
     * @see http://oauth.net/core/1.0a/#anchor46
     *
     * @param  array   $requestParameters An array of request parameters.
     * @param  string  $requestMethod     The request method (GET, POST, ...)
     * @param  string  $requestUrl        The request URL (http://oauth.net/core/1.0/#rfc.section.9.1.2)
     * @return boolean
     */
    public function validateRequest($requestParameters, $requestMethod, $requestUrl);
}
