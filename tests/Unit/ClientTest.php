<?php

namespace Tests\Unit;

use MHD\Peoplevox\Api\Client;
use MHD\Peoplevox\Api\PeoplevoxException;
use MHD\Peoplevox\Api\SessionProvider;
use MHD\Peoplevox\Data\Session;
use PHPUnit\Framework\TestCase;
use SoapClient;

class ClientTest extends TestCase
{
    /**
     * @dataProvider exceptionIsThrownOnErrorResponseDataProvider
     */
    public function testExceptionIsThrownOnErrorResponse(
        string $action,
        object $response,
        string $expectedExceptionMessage
    ) {
        $soapClient = $this->createMock(SoapClient::class);
        $soapClient->method('__soapCall')->willReturn($response);

        $sessionProvider = $this->createMock(SessionProvider::class);
        $sessionProvider->method('getSession')->willReturn(new Session('foo', 'bar'));

        $client = new Client($soapClient, $sessionProvider);

        $this->expectException(PeoplevoxException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $client->callApi($action, ['foo' => 'bar']);
    }

    public function exceptionIsThrownOnErrorResponseDataProvider()
    {
        yield [
            "GetData",
            (object)[
                "GetDataResult" => (object)[
                    'ResponseId' => -1,
                    'Detail' => 'Error: foo'
                ]
            ],
            "Error: foo"
        ];

        yield [
            "SaveData",
            (object)[
                "SaveDataResult" => (object)[
                    'ResponseId' => -1,
                    'Detail' => 'Error: bar'
                ]
            ],
            "Error: bar"
        ];
    }

    /**
     * @dataProvider callApiResultDataProvider
     */
    public function testCallApiResult(string $action, object $soapResponse, object $expectedResponse)
    {
        $soapClient = $this->createMock(SoapClient::class);
        $soapClient->method('__soapCall')->willReturn($soapResponse);

        $sessionProvider = $this->createMock(SessionProvider::class);
        $sessionProvider->method('getSession')->willReturn(new Session('foo', 'bar'));

        $client = new Client($soapClient, $sessionProvider);
        $callApiResponse = $client->callApi($action, ['foo' => 'bar']);

        $this->assertEquals($callApiResponse, $expectedResponse);
    }

    public function callApiResultDataProvider()
    {
        yield [
            "GetData",
            (object)[
                "GetDataResult" => (object)[
                    'ResponseId' => 0,
                    'Detail' => 'foo',
                    'TotalCount' => 9001,
                ]
            ],
            (object)[
                "ResponseId" => 0,
                "Detail" => "foo",
                "TotalCount" => 9001,
            ]
        ];

        yield [
            "SaveData",
            (object)[
                "SaveDataResult" => (object)[
                    'ResponseId' => 0,
                    'Detail' => 'bar',
                ]
            ],
            (object)[
                "ResponseId" => 0,
                "Detail" => "bar",
            ]
        ];
    }

    public function testGetData()
    {
        $soapClient = $this->createMock(SoapClient::class);
        $soapClient->method('__soapCall')->willReturn(
            (object)[
                'GetDataResult' => (object)[
                    'ResponseId' => 0,
                    'Detail' => 'foobar',
                    'TotalCount' => 1234
                ]
            ]
        );

        $sessionProvider = $this->createMock(SessionProvider::class);
        $sessionProvider->method('getSession')->willReturn(new Session('foo', 'bar'));

        $client = new Client($soapClient, $sessionProvider);
        $response = $client->getData('Sales orders');

        $this->assertEquals('foobar', $response->csvData);
        $this->assertEquals(1234, $response->totalCount);
    }

    public function testSaveData()
    {
        $soapClient = $this->createMock(SoapClient::class);
        $soapClient->method('__soapCall')->willReturn(
            (object)[
                'SaveDataResult' => (object)[
                    'ResponseId' => 0,
                    'Detail' => '',
                    'TotalCount' => 12
                ]
            ]
        );

        $sessionProvider = $this->createMock(SessionProvider::class);
        $sessionProvider->method('getSession')->willReturn(new Session('foo', 'bar'));

        $client = new Client($soapClient, $sessionProvider);
        $response = $client->saveData('Sales orders', 'foo,bar', 0);

        $this->assertEquals(12, $response);
    }

    public function testGetSaveTemplate()
    {
        $soapClient = $this->createMock(SoapClient::class);
        $soapClient->method('__soapCall')->willReturn(
            (object)[
                'GetSaveTemplateResult' => (object)[
                    'ResponseId' => 0,
                    'Detail' => 'foo,bar',
                ]
            ]
        );

        $sessionProvider = $this->createMock(SessionProvider::class);
        $sessionProvider->method('getSession')->willReturn(new Session('foo', 'bar'));

        $client = new Client($soapClient, $sessionProvider);
        $response = $client->getSaveTemplate('Sales orders');

        $this->assertEquals('foo,bar', $response);
    }
}
