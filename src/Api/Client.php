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

    public const EVENT_TYPE_AVAILABILITY_CHANGES = 'AvailabilityChanges';
    public const EVENT_TYPE_SALES_ORDER_STATUS_CHANGES = 'SalesOrderStatusChanges';
    public const EVENT_TYPE_GOODS_RECEIVED = 'GoodsReceived';
    public const EVENT_TYPE_TRACKING_NUMBER_RECEIVED = 'TrackingNumberReceived';
    public const EVENT_TYPE_INCREMENTAL_CHANGES = 'IncrementalChanges';
    public const EVENT_TYPE_RETURNS = 'Returns';
    public const EVENT_TYPE_DESPATCH_PACKAGE_TRACKING_NUMBER_RECEIVED = 'DespatchPackageTrackingNumberReceived';
    public const EVENT_TYPE_ON_DESPATCH_ORDER_RECEIVED = 'OnDespatchOrderReceived';
    public const EVENT_TYPE_DESPATCH_PACKAGE_DESPATCHED = 'DespatchPackageDespatched';

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

    /**
     * @link https://peoplevox.github.io/Documentation/#2131-subscribeevent
     * @link https://peoplevox.github.io/Documentation/#2134-event-subscription-data-types
     */
    public function subscribeEvent(
        string $eventType,
        string $callbackUrl,
        string $filter = '',
        ?bool  $encodeParameterData = null
    ): int {
        $data = [
            'eventType' => $eventType,
            'filter' => $filter,
            'callbackUrl' => $callbackUrl,
            'encodeParameterData' => $encodeParameterData
        ];

        $result = $this->callApi('SubscribeEvent', $data);

        return $result->Detail;
    }

    /**
     * @link https://peoplevox.github.io/Documentation/#2132-subscribepostevent
     * @link https://peoplevox.github.io/Documentation/#2134-event-subscription-data-types
     */
    public function subscribePostEvent(
        string $eventType,
        string $postUrl,
        string $postParams = '',
        string $filter = '',
        ?bool  $encodeParameterData = null
    ): int {
        $data = [
            'eventType' => $eventType,
            'filter' => $filter,
            'postUrl' => $postUrl,
            'postParams' => $postParams,
            'encodeParameterData' => $encodeParameterData
        ];

        $result = $this->callApi('SubscribePostEvent', $data);

        return $result->Detail;
    }

    /**
     * @link https://peoplevox.github.io/Documentation/#2135-unsubscribeevent
     */
    public function unsubscribeEvent(int $subscriptionId)
    {
        $result = $this->callApi('UnsubscribeEvent', ['subscriptionId' => $subscriptionId]);

        return $result->Detail;
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
