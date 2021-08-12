<?php

namespace Tests\Unit;

use MHD\Peoplevox\Api\Client;
use MHD\Peoplevox\Utils\SaveTemplateGenerator;
use Nette\PhpGenerator\PsrPrinter;
use PHPUnit\Framework\TestCase;

class SaveTemplateGeneratorTest extends TestCase
{
    /**
     * @dataProvider getClassNameDataProvider
     */
    public function testGetClassName(string $templateName, string $expectedClassName)
    {
        $className = SaveTemplateGenerator::getClassName($templateName);

        $this->assertEquals($expectedClassName, $className);
    }

    public function getClassNameDataProvider()
    {
        yield [
            'Customers',
            'SaveCustomersTemplate'
        ];

        yield [
            'Sales orders',
            'SaveSalesOrdersTemplate'
        ];

        yield [
            'Sales order items',
            'SaveSalesOrderItemsTemplate'
        ];
    }

    /**
     * @dataProvider generateTemplateDataProvider
     */
    public function testGenerateTemplate(
        string $templateName,
        string $namespace,
        string $clientResponse,
        string $expectedTemplate
    ) {
        $client = $this->createMock(Client::class);
        $client->method('getSaveTemplate')->willReturn($clientResponse);

        $generator = new SaveTemplateGenerator($client, new PsrPrinter());
        $generatedTemplate = $generator->generateTemplate($templateName, $namespace);

        $this->assertEquals($expectedTemplate, $generatedTemplate);
    }

    public function generateTemplateDataProvider()
    {
        yield [
            'Sales orders',
            '',
            'SalesOrderNumber,Customer',
            <<<'TEMPLATE'
<?php

class SaveSalesOrdersTemplate
{
    public $SalesOrderNumber;
    public $Customer;
}

TEMPLATE
        ];

        yield [
            'Sales order items',
            'Generated',
            'SalesOrderNumber,SKU',
            <<<'TEMPLATE'
<?php

namespace Generated;

class SaveSalesOrderItemsTemplate
{
    public $SalesOrderNumber;
    public $SKU;
}

TEMPLATE
        ];
    }
}
