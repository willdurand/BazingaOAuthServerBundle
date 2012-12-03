BazingaOAuthServerBundle
========================

[![Build Status](https://secure.travis-ci.org/willdurand/BazingaOAuthServerBundle.png)](http://travis-ci.org/willdurand/BazingaOAuthServerBundle)

This bundle provides all you need to manage OAuth in a server side way.

This bundle implements the **OAuth v1.0** protocol based on the **RFC 5849**.

**NOTE: for now, there is no implementation of the Model. You have to implement all interfaces in
the `Model/` folder and the logic behind.**


## Installation
As usual, add this bundle with composer:

    composer require willdurand/oauth-server-bundle

Register the bundle in `app/AppKernel.php`:

``` php
<?php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new Bazinga\OAuthServerBundle\BazingaOAuthServerBundle(),
    );
}
```

Import the `routing.yml` configuration file in `app/config/routing.yml`:

``` yaml
# app/config/routing.yml
bazinga_oauth:
    resource: "@BazingaOAuthServerBundle/Resources/config/routing/routing.yml"
```

That's all for the installation :-)


## Configuration
In order to use this bundle, you have to configure it.

First, you have to implement three services:

* a **consumer provider** service by implementing the `OAuthConsumerProviderInterface` interface;
* a **token provider** service by implementing the `OAuthTokenProviderInterface` interface;
* a **nonce provider** service by implementing the `OAuthNonceProviderInterface` interface.

These services have to be registered in the Dependency Injection container.

Example:

``` yaml
services:
    acme.my_consumer_provider:
        class: Acme\DemoBundle\Model\Provider\MyConsumerProvider
        arguments:
            -  @doctrine.orm.entity_manager
    acme.my_token_provider:
        class: Acme\DemoBundle\Model\Provider\MyTokenProvider
        arguments:
            -  @doctrine.orm.entity_manager
    acme.my_nonce_provider:
        class: Acme\DemoBundle\Model\Provider\MyNonceProvider
        arguments:
            -  @doctrine.orm.entity_manager
```

Once done, add the following lines to your configuration file `app/config/config.yml`:

``` yaml
# app/config/config.yml
bazinga_o_auth_server:
    enable_xauth:   false
    service:
        consumer_provider: acme.my_consumer_provider
        token_provider:    acme.my_token_provider
        nonce_provider:    acme.my_nonce_provider
```

Now, you need to configure the **security** component:

``` yaml
# app/config/security.yml
security:
    providers:
        # ...
        # This provider will be used to authenticate consumers
        oauth_consumer_provider:
            id: acme.my_consumer_provider

    firewalls:
        # The following part is about 'form_login' authentication
        # process which is the common case. Feel free to choose your
        # own authentication process. Keep in mind that you have to
        # secure the following URL: '/oauth/login/allow'.
        #
        # A user can access this URL with the 'IS_AUTHENTICATED_FULLY' role.
        # This URL will present a form to the user and will ask him to
        # allow or to revoke the consumer to access its information.
        #
        # Example for the 'form_login' process:
        #
        oauth_login:
            pattern: ^/oauth/login$
            security: false

        oauth_area:
            pattern:  ^/oauth/login
            form_login:
                check_path: /oauth/login_check
                login_path: /oauth/login
```

It's your job to write this part. You will have to declare two new routes:

* `/oauth/login_check`
* `/oauth/login`

And to create the logic behind these routes is also your job ! For instance:

``` php
<?php
// src/Acme/DemoBundle/Controller/LoginController.php

namespace Acme\DemoBundle\Controller;

class LoginController
{
    // ...

    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->engine->renderResponse('DemoBundle:Login:login.html.twig', array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }

    public function loginCheckAction()
    {
    }
}
```

Routing definition:

``` yaml
# src/Acme/DemoBundle/Resources/config/routing.yml
acme_demo_login_login:
    pattern:    /oauth/login
    defaults:   { _controller: acme.demo.controller.login:loginAction }

acme_demo_login_login_check:
    pattern:    /oauth/login_check
    defaults:   { _controller: acme.demo.controller.login:loginCheckAction }
```

View:

``` django+jinja
{# src/Acme/DemoBundle/Resources/config/Login/login.html.twig #}

{% if error %}
    <div>{{ error }}</div>
{% endif %}

<form action="{{ path('acme_demo_login_login_check') }}" method="post">
    <label for="username">{{ 'security.login.username'|trans({}, 'AcmeDemoBundle') }}</label>
    <input type="text" id="username" name="_username" value="{{ last_username }}" />

    <label for="password">{{ 'security.login.password'|trans({}, 'AcmeDemoBundle') }}</label>
    <input type="password" id="password" name="_password" />

    <input type="submit" id="_submit" name="_submit" value="{{ 'security.login.submit'|trans({}, 'AcmeDemoBundle') }}" />
</form>
```

Most of the time, OAuth clients use modal boxes to present the OAuth forms.
So keep in mind this point when you will create your templates.

At this time, everything is configured. If you go to `/oauth/login/allow`, it should redirect
you to the authentication mechanism you just configured.


## Usage
To secure a part of your application by using OAuth, you just have to declare a new pattern
in the **firewall** configuration:

``` yaml
# app/config/security.yml
security:
    firewalls:
        # ...

        # OAuth security
        secured_api:
            pattern:    ^/api/
            bazinga_oauth: true
```


## xAuth

**THIS PART IS NOT WORKING AT THE MOMENT !**

[xAuth](http://dev.twitter.com/pages/xauth) is still OAuth but it provides a way to exchange username
ans password for an OAuth access token. Be sure to approve applications that use this protocol.

To enable it in your application, just set the `enable_xauth` configuration parameter to `true`.

``` yaml
# app/config/config.yml
bazinga_o_auth:
    enable_xauth:   true
```


## Extending things
This bundle provides a set of interfaces that help you to create your own implementation
that fit your needs. The following part will explain you how to extend things.

### Model
By implementing all interfaces contained in the `Model/` directory, you will be able to customize the
model to fit your needs.

### Signature
The OAuth RFC says there are at least two signature methods to implement : PLAINTEXT
and HMAC-SHA1. There is a third common signature which is RSA-SHA1 but it's not yet implemented
in this bundle. The RFC also says that you can add your own signatures like MD5 or whatever.

To add a new signature, it's pretty easy. You just have to create a class that implements the
`OAuthSignatureInterface` and to register it as a service tagged with `oauth.signature_service`.

``` xml
<service id="my.oauth.signature.impl" class="%my.oauth.signature.impl.class%">
    <tag name="oauth.signature_service" />
</service>
```

### OAuth server service
Maybe you want to improve the basic OAuth service implementation, to add custom parameters for your API.
That's possible, thanks to the container. The easiest way to change the provided service implementation
by your own is to change the parameter `bazinga.oauth.server_service.class`.

Your custom class must implement the `OAuthServerServiceInterface`. The `OAuthAbstractServerService` class
provides useful methods that can help you to create your own service. It's recommended to extends this class.

### The authorization view
You can change the default authorization view (which ask the user to accept or revoke a consumer to access
its data) by overriding the two templates: `authorize.html.twig` and `error.html.twig` found in the
`Resources/views/` directory.


## Links

* [RFC 5849](http://tools.ietf.org/html/rfc5849)
* [OAuth core 1.0a](http://oauth.net/core/1.0a/)
* [xAuth by Twitter](http://dev.twitter.com/pages/xauth)
* [Yahoo OAuth Authorization flow](http://developer.yahoo.com/oauth/guide/oauth-auth-flow.html)


## Credits

* William DURAND (Bazinga).
* Inspirated by the [oauth project](http://code.google.com/p/oauth/) on Google code for the Signature part.
