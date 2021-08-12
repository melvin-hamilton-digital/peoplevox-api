<?php

namespace MHD\Peoplevox\Api;

use MHD\Peoplevox\Data\GetDataResponse;
use SoapClient;
use SoapHeader;

class Client
{
    public const ACTION_NO_ACTION = 0;
    public const ACTION_DO_NOT_ALLOCATE = 1;
    public const ACTION_DELETE = 2;

    /**
     * @var SoapClient
     */
    private $soapClient;

    /**
     * @var SessionProvider
     */
    private $sessionProvider;

    public function __construct(
        SoapClient      $soapClient,
        SessionProvider $sessionProvider
    ) {
        $this->soapClient = $soapClient;
        $this->sessionProvider = $sessionProvider;
    }

    /**
     * @link https://peoplevox.github.io/Documentation/#2111-getdata
     * @link https://peoplevox.github.io/Documentation/#222-search-values
     * @link https://peoplevox.github.io/Documentation/#224-getrequest
     * @link https://peoplevox.github.io/Documentation/#227-templates
     */
    public function getData(
        string $templateName,
        int    $page = 1,
        int    $pageSize = 100,
        string $searchClause = ''
    ): GetDataResponse {
        $data = [
            'TemplateName' => $templateName,
            'PageNo' => $page,
            'ItemsPerPage' => $pageSize,
            'SearchClause' => $searchClause,
        ];

        $result = $this->callApi('GetData', ['getRequest' => $data]);

        return new GetDataResponse($result->Detail, $result->TotalCount);
    }

    /**
     * @link https://peoplevox.github.io/Documentation/#2121-getsavetemplate
     */
    public function getSaveTemplate(string $templateName): string
    {
        $result = $this->callApi('GetSaveTemplate', ['templateName' => $templateName]);

        return $result->Detail;
    }

    /**
     * @link https://peoplevox.github.io/Documentation/#2122-savedata
     * @link https://peoplevox.github.io/Documentation/#226-saverequest
     * @link https://peoplevox.github.io/Documentation/#227-templates
     */
    public function saveData(
        string $templateName,
        string $csvData,
        int    $action
    ): int {
        $data = [
            'TemplateName' => $templateName,
            'CsvData' => $csvData,
            'Action' => $action
        ];

        $result = $this->callApi('SaveData', ['saveRequest' => $data]);

        return $result->TotalCount;
    }

    public function callApi(string $action, array $data)
    {
        $sessionHeader = new SoapHeader(
            "http://www.peoplevox.net/",
            "UserSessionCredentials",
            (array)$this->sessionProvider->getSession()
        );

        $response = $this->soapClient->__soapCall($action, [$data], null, [$sessionHeader]);
        $result = "{$action}Result";

        if ($response->$result->ResponseId === -1) {
            throw new PeoplevoxException($response->$result->Detail);
        }

        return $response->$result;
    }
}
