<?php

namespace Tests\Unit;

use DateTime;
use MHD\Peoplevox\Data\SearchClause;
use PHPUnit\Framework\TestCase;

class SearchClauseTest extends TestCase
{
    /**
     * @dataProvider formatValueDataProvider
     */
    public function testFormatValue($value, string $expected)
    {
        $this->assertEquals($expected, SearchClause::formatValue($value));
    }

    public function formatValueDataProvider()
    {
        yield [1, '1'];
        yield [new DateTime('@0'), 'DateTime(1970,01,01,00,00,00)'];
        yield ['foo', '"foo"'];
    }

    /**
     * @dataProvider fieldValueEqualToDataProvider
     */
    public function testFieldValueEqualTo(string $field, $value, string $expected)
    {
        $this->assertEquals($expected, SearchClause::fieldValueEqualTo($field, $value));
    }

    public function fieldValueEqualToDataProvider()
    {
        yield ["foo", 1, "foo == 1"];
        yield ["bar", new DateTime('@1234567890'), "bar == DateTime(2009,02,13,23,31,30)"];
        yield ["name", "John", "name == \"John\""];
    }

    public function testFieldValueContains()
    {
        $this->assertEquals("foo.Contains(\"bar\")", SearchClause::fieldValueContains("foo", "bar"));
    }

    public function testFieldValueStartsWith()
    {
        $this->assertEquals("bar.StartsWith(\"baz\")", SearchClause::fieldValueStartsWith("bar", "baz"));
    }

    public function testFieldValueEndsWith()
    {
        $this->assertEquals("spam.EndsWith(\"eggs\")", SearchClause::fieldValueEndsWith("spam", "eggs"));
    }

    public function testFieldValueBetween()
    {
        $this->assertEquals("foo >= 0 AND foo <= 9000", SearchClause::fieldValueBetween("foo", 0, 9000));
    }

    /**
     * @dataProvider fieldValueInDataProvider
     */
    public function testFieldValueIn(string $field, array $haystack, string $expected)
    {
        $this->assertEquals($expected, SearchClause::fieldValueIn($field, $haystack));
    }

    public function fieldValueInDataProvider()
    {
        yield [
            "foo",
            [1],
            "foo == 1",
        ];

        yield [
            "bar",
            ["foo", "bar"],
            "bar == \"foo\" OR bar == \"bar\"",
        ];

        yield [
            "foo",
            [1, 2, 3, 4],
            "foo == 1 OR foo == 2 OR foo == 3 OR foo == 4"
        ];
    }
}
