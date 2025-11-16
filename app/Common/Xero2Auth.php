<?php

namespace App\Common;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use XeroAPI\XeroPHP\Configuration;
use XeroAPI\XeroPHP\JWTClaims;
use GuzzleHttp\Client;
use XeroAPI\XeroPHP\Api\IdentityApi;
use Exception;

class Xero2Auth
{
    private $provider;
    public function __construct()
    {

        $this->provider = new GenericProvider([
            'clientId'                => config('services.xero.client_id'),
            'clientSecret'            => config('services.xero.client_secret'),
            'redirectUri'             => config('services.xero.redirect_uri'),
            'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
            'urlAccessToken'          => 'https://identity.xero.com/connect/token',
            'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
        ]);
    }

    public function connect()
    {
        // Scope defines the data your app has permission to access.
        // Learn more about scopes at https://developer.xero.com/documentation/oauth2/scopes
        $options = [
            'scope' => ['offline_access accounting.contacts accounting.settings accounting.transactions']
        ];

        // This returns the authorizeUrl with necessary parameters applied (e.g. state).
        $authorizationUrl = $this->provider->getAuthorizationUrl($options);

        // Save the state generated for you and store it to the session.
        // For security, on callback we compare the saved state with the one returned to ensure they match.
        $oauth2state = $this->provider->getState();

        return [
            'authorization_url' => $authorizationUrl,
            'oauth2_state' => $oauth2state
        ];
    }

    public function get_access_token($authorization_code)
    {
        try {
            // Try to get an access token using the authorization code grant.
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $authorization_code
            ]);

            $config = Configuration::getDefaultConfiguration()->setAccessToken( (string)$accessToken->getToken() );
            $identityApi = new IdentityApi(
                new Client(),
                $config
            );
            $jwt = new JWTClaims();
            $jwt->decodeAccessToken((string)$accessToken->getToken());
            $eventId = $jwt->getAuthenticationEventId();

            $connections = collect($identityApi->getConnections());
            $result = $connections->firstWhere('auth_event_id', $eventId);

            if( $result == null )
            {
                return [
                    'status' => 'FAILED'
                ];
            }
            // Save my tokens, expiration tenant_id
            return [
                'status' => 'SUCCESS',
                'data' => [
                    'token' => $accessToken->getToken(),
                    'expiry' => $accessToken->getExpires(),
                    'tenant_id' => $result->getTenantId(),
                    'refresh_token' => $accessToken->getRefreshToken(),
//                    'id_token' => $accessToken->getValues()["id_token"]
                ]
            ];
        } catch (IdentityProviderException $e) {
            $error = $e->getResponseBody();
            return [
                'status' => 'FAILED',
                'error' => $error['Detail']
            ];
        } catch (Exception $e) {
            return [
                'status' => 'FAILED',
                'error' => $e->getMessage()
            ];
        }

    }

    public function get_new_access_token($refresh_token)
    {
        try {
            $newAccessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $refresh_token
            ]);

            // Save my tokens, expiration tenant_id
            return [
                'status' => 'SUCCESS',
                'data' => [
                    'token' => $newAccessToken->getToken(),
                    'expiry' => $newAccessToken->getExpires(),
                    'refresh_token' => $newAccessToken->getRefreshToken(),
//                    'id_token' => $newAccessToken->getValues()["id_token"]
                ]
            ];
        } catch (IdentityProviderException $e) {
            return [
                'status' => 'FAILED',
                'error' => $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'FAILED',
                'error' => $e->getMessage()
            ];
        }

    }
}
