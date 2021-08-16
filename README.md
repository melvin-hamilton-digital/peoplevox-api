# Peoplevox API

## API documentation

https://peoplevox.github.io/Documentation/

## Current features

* data export,
* data import,
* code generation for data import templates.

## Example usage

```php
use MHD\Peoplevox\Api\Client;
use MHD\Peoplevox\Api\SessionProvider;
use MHD\Peoplevox\Data\Credentials;
use MHD\Peoplevox\Data\SearchClause;

$wsdl = "https://{your-WMS-Web-Address}/resources/integrationservicev4.asmx?wsdl";
$soapClient = new SoapClient($wsdl);
$credentials = new Credentials('clientId', 'username', 'password');

$sessionProvider = new SessionProvider($soapClient, $credentials);
$peoplevoxClient = new Client($soapClient, $sessionProvider);

# get sales orders
$salesOrders = $peoplevoxClient->getData('Sales orders');
# ...

# get sales orders using search clause
$salesOrders = $peoplevoxClient->getData(
    'Sales orders',
    1,
    10,
    SearchClause::fieldValueIn("Customer", ["John Doe", "Jane Doe"])
);
# ...
```

## Save template generation

In order to simplify save requests `SaveTemplateGenerator` class was created. Generated classes' properties will match
your templates' configuration and should simplify imports.

```php
use MHD\Peoplevox\Utils\SaveTemplateGenerator;
use Nette\PhpGenerator\PsrPrinter;

# generate template
$saveTemplateGenerator = new SaveTemplateGenerator(
    $peoplevoxClient,
    new PsrPrinter()
);
$generatedTemplate = $saveTemplateGenerator->generateTemplate(
    'Sales orders',
    'Generated'
);
file_put_contents(
    './Generated/SaveSalesOrdersTemplate.php',
    $generatedTemplate
);

# use generated template
require_once './Generated/SaveSalesOrdersTemplate.php';

$newOrder = new Generated\SaveSalesOrdersTemplate();
$newOrder->SalesOrderNumber = '1234';
$newOrder->Customer = 'John Doe';
# ...
```

## Event subscription

When subscribing to events, please keep in mind, that currently it is not possible to retrieve the list of already
subscribed events, neither from the Peoplevox web panel nor API. You should consider saving at least the event type and
returned subscription ID to keep track of already subscribed events and be able to unsubscribe in the future.

```php
use MHD\Peoplevox\Api\Client;

$subscriptionId = $peoplevoxClient->subscribePostEvent(
    Client::EVENT_TYPE_AVAILABILITY_CHANGES,
    'https://example.org/ProcessAvailabilityChanges',
    'item={ItemCode}&amp;available={Available}'
);
# save event type and subscription ID
```
