# Peoplevox API

## Example usage

```php
use MHD\Peoplevox\Api\Client;
use MHD\Peoplevox\Api\SessionProvider;
use MHD\Peoplevox\Data\Credentials;
use MHD\Peoplevox\Data\SearchClause;

$wsdl = "https://wms.peoplevox.net/myclientid/integrationservicev4.asmx";
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
In order to simplify save requests `SaveTemplateGenerator` class was created.
Generated classes' properties will match your templates' configuration and should simplify imports.

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
