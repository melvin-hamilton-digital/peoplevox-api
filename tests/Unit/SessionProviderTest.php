<?php

namespace Tests\Unit;

use MHD\Peoplevox\Api\PeoplevoxException;
use MHD\Peoplevox\Api\SessionProvider;
use MHD\Peoplevox\Data\Credentials;
use PHPUnit\Framework\TestCase;
use SoapClient;

class SessionProviderTest extends TestCase
{
    public function testExceptionIsThrownIfAuthenticationFails()
    {
        $soapClient = $this->createMock(SoapClient::class);
        $credentials = new Credentials('foo', 'bar', 'baz');
        $sessionProvider = new SessionProvider($soapClient, $credentials);

        $response = (object)[
            "AuthenticateResult" => (object)[
                "ResponseId" => -1,
                "Detail" => "Error: Failed to authenticate",
            ]
        ];
        $soapClient->method('__soapCall')->willReturn($response);
        $this->expectException(PeoplevoxException::class);
        $this->expectExceptionMessage("Error: Failed to authenticate");
        $sessionProvider->startSession();
    }

    public function testStartSession()
    {
        $soapClient = $this->createMock(SoapClient::class);
        $credentials = new Credentials('foo', 'bar', 'baz');
        $sessionProvider = new SessionProvider($soapClient, $credentials);

        $response = (object)[
            "AuthenticateResult" => (object)[
                "ResponseId" => 0,
                "Detail" => "foo,01efee1b1565814930233d07eba4a0a7",
            ]
        ];
        $soapClient->method('__soapCall')->willReturn($response);
        $session = $sessionProvider->startSession();

        $this->assertEquals('foo', $session->ClientId);
        $this->assertEquals('01efee1b1565814930233d07eba4a0a7', $session->SessionId);
    }
}
