<?php

namespace MHD\Peoplevox\Utils;

use MHD\Peoplevox\Api\Client;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;

class SaveTemplateGenerator
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var PsrPrinter
     */
    private $psrPrinter;

    public function __construct(
        Client     $client,
        PsrPrinter $psrPrinter
    ) {
        $this->client = $client;
        $this->psrPrinter = $psrPrinter;
    }

    public static function getClassName(string $templateName): string
    {
        $parts = explode(' ', $templateName);
        foreach ($parts as &$part) {
            $part = ucfirst(strtolower($part));
        }

        return 'Save' . implode('', $parts) . 'Template';
    }

    public function generateTemplate(string $templateName, string $namespace = ''): string
    {
        $namespace = new PhpNamespace($namespace);

        $className = self::getClassName($templateName);
        $class = $namespace->addClass($className);

        $template = $this->client->getSaveTemplate($templateName);
        $properties = str_getcsv($template);

        foreach ($properties as $property) {
            $class->addProperty($property);
        }

        return "<?php\n\n" . $this->psrPrinter->printNamespace($namespace);
    }
}
