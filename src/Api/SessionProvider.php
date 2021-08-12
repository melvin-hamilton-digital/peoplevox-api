<?php

namespace MHD\Peoplevox\Api;

use MHD\Peoplevox\Data\Credentials;
use MHD\Peoplevox\Data\Session;
use SoapClient;

/**
 * @link https://peoplevox.github.io/Documentation/#21-api-calls
 */
class SessionProvider
{
    /**
     * @var SoapClient
     */
    private $soapClient;

    /**
     * @var Credentials
     */
    private $credentials;

    /**
     * @var Session
     */
    private $session;

    public function __construct(
        SoapClient  $soapClient,
        Credentials $credentials
    ) {
        $this->soapClient = $soapClient;
        $this->credentials = $credentials;
    }

    public function getSession(): Session
    {
        if (!$this->session) {
            $this->session = $this->startSession();
        }

        return $this->session;
    }

    public function startSession(): Session
    {
        $authenticationResponse = $this->authenticate();
        if ($authenticationResponse->AuthenticateResult->ResponseId === -1) {
            throw new PeoplevoxException($authenticationResponse->AuthenticateResult->Detail);
        }

        return new Session(...explode(',', $authenticationResponse->AuthenticateResult->Detail));
    }

    public function authenticate()
    {
        $parameters = [
            'clientId' => $this->credentials->clientId,
            'username' => $this->credentials->username,
            'password' => base64_encode($this->credentials->password),
        ];

        return $this->soapClient->__soapCall('Authenticate', [$parameters]);
    }
}
