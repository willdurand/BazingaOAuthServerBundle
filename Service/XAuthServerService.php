<?php

namespace Bazinga\OAuthServerBundle\Service;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Bazinga\OAuthServerBundle\Service\OAuthServerService;

/**
 * xAuth implementation.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class XAuthServerService extends OAuthServerService
{
    protected $userAuthenticationProvider;

    protected $providerKey;

    /**
     * An array of required parameters names for the access resource process
     * by using xAuth protocol.
     * @var array
     */
    protected $requiredParamsForXAuth = array(
        'oauth_consumer_key',
        'oauth_nonce',
        'oauth_signature_method',
        'oauth_timestamp',
        'x_auth_mode',
        'x_auth_password',
        'x_auth_username'
    );

    /**
     * Set the user authentication provider.
     *
     * @param $userAuthenticationProvider
     */
    public function setUserAuthenticationProvider($userAuthenticationProvider)
    {
        $this->userAuthenticationProvider = $userAuthenticationProvider;
    }

    /**
     * Set the provider key.
     *
     * @param $providerKey
     */
    public function setProviderKey($providerKey)
    {
        $this->providerKey = $providerKey;
    }

    /**
     * {@inheritdoc}
     */
    protected function checkRequirements($requestParameters, array $requiredParameters = array())
    {
        parent::checkRequirements($requestParameters, $requiredParameters);

        // according to the doc, 'x_auth_mode' must be 'client_auth'
        if ('client_auth' !== $requestParameters['x_auth_mode']) {
            throw new HttpException(400);
        }
    }

    /**
     * {@inheritdoc}
     * Implements the interface.
     */
    public function accessToken($requestParameters, $requestMethod, $requestUrl)
    {
        if (! isset($requestParameters['oauth_token'])) {
            // xAuth access token does not have the 'oauth_token' parameter
            $this->checkRequirements($requestParameters, $this->requiredParamsForXAuth);
        } else {
            return parent::accessToken($requestParameters, $requestMethod, $requestUrl);
        }

        $consumer = $this->getConsumerByKey($requestParameters['oauth_consumer_key']);

        try {
            $token = new UsernamePasswordToken(
                $requestParameters['x_auth_username'],
                $requestParameters['x_auth_password'],
                $this->providerKey,
                array('XAUTH')
            );
            $token = $this->userAuthenticationProvider->authenticate($token);
        } catch (\Exception $e) {
            throw new HttpException(401);
        }

        return $this->createAccessToken($consumer, $token->getUser(), $requestParameters, $requestMethod, $requestUrl, $requestToken);
    }
}
