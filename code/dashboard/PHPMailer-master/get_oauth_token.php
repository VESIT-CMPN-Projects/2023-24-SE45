<?php


namespace League\OAuth2\Client\Provider;

require 'vendor/autoload.php';

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

session_start();

//If this automatic URL doesn't work, set it yourself manually
$redirectUri = isset($_SERVER['HTTPS']) ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];


//These details obtained are by setting up app in Google developer console.
$clientId = 'RANDOMCHARS-----duv1n2.apps.googleusercontent.com';
$clientSecret = 'RANDOMCHARS-----lGyjPcRtvP';

class Google extends AbstractProvider
{
    use BearerAuthorizationTrait;

    const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'id';

   
    protected $accessType;

   
    protected $hostedDomain;

    
    protected $scope;

    public function getBaseAuthorizationUrl()
    {
        return 'https://accounts.google.com/o/oauth2/auth';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://accounts.google.com/o/oauth2/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
	return ' ';
    }

    protected function getAuthorizationParameters(array $options)
    {
	if (is_array($this->scope)) {
            $separator = $this->getScopeSeparator();
            $this->scope = implode($separator, $this->scope);
        }

        $params = array_merge(
            parent::getAuthorizationParameters($options),
            array_filter([
                'hd'          => $this->hostedDomain,
                'access_type' => $this->accessType,
		'scope'       => $this->scope,
                // if the user is logged in with more than one account ask which one to use for the login!
                'authuser'    => '-1'
            ])
        );
        return $params;
    }

    protected function getDefaultScopes()
    {
        return [
            'email',
            'openid',
            'profile',
        ];
    }

    protected function getScopeSeparator()
    {
        return ' ';
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            $code  = 0;
            $error = $data['error'];

            if (is_array($error)) {
                $code  = $error['code'];
                $error = $error['message'];
            }

            throw new IdentityProviderException($error, $code, $data);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new GoogleUser($response);
    }
}


//Set Redirect URI in Developer Console as [https/http]://<yourdomain>/<folder>/get_oauth_token.php
$provider = new Google(
    array(
        'clientId' => $clientId,
        'clientSecret' => $clientSecret,
        'redirectUri' => $redirectUri,
        'scope' => array('https://mail.google.com/'),
	'accessType' => 'offline'
    )
);

if (!isset($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: ' . $authUrl);
    exit;
// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
} else {
    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken(
        'authorization_code',
        array(
            'code' => $_GET['code']
        )
    );

    // Use this to get a new access token if the old one expires
    echo 'Refresh Token: ' . $token->getRefreshToken();
}
