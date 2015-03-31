<?php

namespace Bazinga\OAuthServerBundle\Service;

use Bazinga\OAuthServerBundle\Model\ConsumerInterface;
use Bazinga\OAuthServerBundle\Model\RequestTokenInterface;
use Bazinga\OAuthServerBundle\Model\AccessTokenInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * OAuth version 1.0.
 *
 * This is the concrete Symfony2 implementation of an OAuth server service.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthServerService extends OAuthAbstractServerService
{
    /**
     * {@inheritdoc}
     * This method has been overrided to add custom exception if error.
     */
    protected function getSignatureService($signatureServiceName)
    {
        $signatureService = parent::getSignatureService($signatureServiceName);

        if (null === $signatureService) {
            // Unsupported signature method
            throw new HttpException(400, self::ERROR_SIGNATURE_METHOD_REJECTED);
        }

        return $signatureService;
    }

    /**
     * Proxy method that handles the error logic.
     * Returns a consumer based on its key.
     *
     * @param  string            $oauth_consumer_key A consumer key.
     * @return ConsumerInterface A consumer or <code>null</code>.
     */
    protected function getConsumerByKey($oauth_consumer_key)
    {
        $consumer = $this->consumerProvider->getConsumerByKey($oauth_consumer_key);

        return $this->checkConsumer($consumer);
    }

    /**
     * Proxy method that handles the error logic.
     * Returns a consumer based on its request token.
     *
     * @param  RequestTokenInterface $requestToken A request token.
     * @return ConsumerInterface     A consumer or <code>null</code>.
     */
    protected function getConsumerByRequestToken(RequestTokenInterface $requestToken)
    {
        return $this->checkConsumer($requestToken->getConsumer());
    }

    /**
     * Proxy method that handles the error logic.
     *
     * @param  string                $oauth_token A request token.
     * @return RequestTokenInterface
     */
    protected function loadRequestToken($oauth_token)
    {
        $token = $this->tokenProvider->loadRequestTokenByToken($oauth_token);

        if (! $token instanceof RequestTokenInterface || $token->hasExpired()) {
            throw new HttpException(401, self::ERROR_TOKEN_REJECTED);
        }

        return $token;
    }

    /**
     * Check that the given parameter is a valid consumer.
     *
     * @param  mixed             $consumer Should be a consumer object.
     * @return ConsumerInterface A consumer.
     */
    protected function checkConsumer($consumer)
    {
        if (! $consumer instanceof ConsumerInterface) {
            throw new HttpException(401, self::ERROR_CONSUMER_KEY_UNKNOWN);
        }

        return $consumer;
    }

    /**
     * Handles the logic to validate mandatory parameters.
     *
     * @param array $requestParameters  An array of request parameters.
     * @param array $requiredParameters An array of required parameter names.
     */
    protected function checkRequirements($requestParameters, array $requiredParameters = array())
    {
        if (null === $requestParameters) {
            throw new HttpException(400, self::ERROR_PARAMETER_ABSENT);
        }

        foreach ($requiredParameters as $requiredParameter) {
            if (false === array_key_exists($requiredParameter, $requestParameters)) {
                throw new HttpException(400, self::ERROR_PARAMETER_ABSENT);
            }
        }

        if (false === $this->checkTimestamp($requestParameters['oauth_timestamp'])) {
            throw new HttpException(400, self::ERROR_TIMESTAMP_REFUSED);
        }

        if (isset($requestParameters['oauth_version']) && false === $this->checkVersion($requestParameters['oauth_version'])) {
            // 'oauth_version' is an optional parameter but presents, it must be equal to OAUTH_VERSION.
            throw new HttpException(400, self::ERROR_VERSION_REJECTED);
        }
    }

    /**
     * Creates an access token if possible.
     *
     * @param ConsumerInterface     $consumer          A consumer.
     * @param UserInterface         $user              A user.
     * @param array                 $requestParameters An array of request parameters.
     * @param string                $requestMethod     The request method.
     * @param string                $requestUrl        The request url.
     * @param RequestTokenInterface $requestToken      A request token.
     *
     * @return string
     */
    protected function createAccessToken(ConsumerInterface $consumer, UserInterface $user, $requestParameters,
        $requestMethod, $requestUrl, RequestTokenInterface $requestToken = null)
    {
        if (true === $this->approveSignature($consumer, $requestToken, $requestParameters, $requestMethod, $requestUrl)) {
            $token = $this->tokenProvider->createAccessToken($consumer, $user);

            if ($token instanceof AccessTokenInterface) {
                if (null !== $requestToken) {
                    $this->tokenProvider->deleteRequestToken($requestToken);
                }

                return $this->sendToken($token, $this->getAccessTokenLifetime());
            } else {
                throw new HttpException(500);
            }
        } else {
            throw new HttpException(401, self::ERROR_SIGNATURE_INVALID);
        }
    }

    /**
     * {@inheritdoc}
     * Implements interface.
     */
    public function requestToken($requestParameters, $requestMethod, $requestUrl)
    {
        $this->checkRequirements($requestParameters, $this->requiredParamsForRequestToken);

        $consumer = $this->getConsumerByKey($requestParameters['oauth_consumer_key']);

        if (true === $this->approveSignature($consumer, null, $requestParameters, $requestMethod, $requestUrl)) {
            return $this->sendToken(
                $this->tokenProvider->createRequestToken($consumer),
                $this->getRequestTokenLifetime(),
                array(
                    'oauth_callback_confirmed' => true
                )
            );
        } else {
            throw new HttpException(401, self::ERROR_SIGNATURE_INVALID);
        }
    }

    /**
     * {@inheritdoc}
     * Implements the interface.
     */
    public function authorize($oauth_token, $oauth_callback = null)
    {
        if (null === $oauth_token) {
            throw new HttpException(401, self::ERROR_PARAMETER_ABSENT);
        }

        $token    = $this->loadRequestToken($oauth_token);
        $consumer = $this->getConsumerByRequestToken($token);

        if (null === $oauth_callback || empty($oauth_callback)) {
            $oauth_callback = $consumer->getCallback();
        }

        $authorizeString = sprintf('oauth_token=%s&oauth_verifier=%s',
            $token->getToken(),
            $token->getVerifier()
        );

        if ('oob' === substr($oauth_callback, 0, 3)) {
            // Out Of Band
            return $authorizeString;
        } else {
            $format = (strpos($oauth_callback, '?') !== false) ? '%s&%s' : '%s?%s';

            return sprintf($format, $oauth_callback, $authorizeString);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function accessToken($requestParameters, $requestMethod, $requestUrl)
    {
        $this->checkRequirements($requestParameters, $this->requiredParamsForAccessToken);

        $consumer     = $this->getConsumerByKey($requestParameters['oauth_consumer_key']);
        $requestToken = $this->loadRequestToken($requestParameters['oauth_token']);

        return $this->createAccessToken($consumer, $requestToken->getUser(), $requestParameters, $requestMethod, $requestUrl, $requestToken);
    }

    /**
     * {@inheritdoc}
     */
    public function validateRequest($requestParameters, $requestMethod, $requestUrl)
    {
        $this->checkRequirements($requestParameters, $this->requiredParamsForValidRequest);

        $consumer = $this->getConsumerByKey($requestParameters['oauth_consumer_key']);
        $token    = $this->tokenProvider->loadAccessTokenByToken($requestParameters['oauth_token']);

        if (false === $this->nonceProvider->checkNonceAndTimestampUnicity($requestParameters['oauth_nonce'], $requestParameters['oauth_timestamp'], $consumer)) {
            throw new HttpException(400, self::ERROR_NONCE_USED);
        } else {
            $this->nonceProvider->registerNonceAndTimestamp($requestParameters['oauth_nonce'], $requestParameters['oauth_timestamp'], $consumer);
        }

        if (! $token instanceof AccessTokenInterface) {
            throw new HttpException(401, self::ERROR_TOKEN_REJECTED);
        }

        if (true !== $this->approveSignature($consumer, $token, $requestParameters, $requestMethod, $requestUrl)) {
            throw new HttpException(401, self::ERROR_SIGNATURE_INVALID);
        }

        if ($token->hasExpired()) {
            $this->tokenProvider->deleteAccessToken($token);
            throw new HttpException(401, self::ERROR_TOKEN_EXPIRED);
        }

        return true;
    }
}
