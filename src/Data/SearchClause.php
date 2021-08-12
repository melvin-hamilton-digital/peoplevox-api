<?php

namespace MHD\Peoplevox\Data;

use DateTime;

/**
 * @link https://peoplevox.github.io/Documentation/#222-search-values
 */
class SearchClause
{
    /**
     * @param int|string|DateTime $value
     */
    public static function fieldValueEqualTo(string $field, $value): string
    {
        $value = self::formatValue($value);

        return "{$field} == {$value}";
    }

    public static function fieldValueContains(string $field, string $needle): string
    {
        return "{$field}.Contains(\"{$needle}\")";
    }

    public static function fieldValueStartsWith(string $field, string $needle): string
    {
        return "{$field}.StartsWith(\"{$needle}\")";
    }

    public static function fieldValueEndsWith(string $field, string $needle): string
    {
        return "{$field}.EndsWith(\"{$needle}\")";
    }

    /**
     * @param int|DateTime $min
     * @param int|DateTime $max
     */
    public static function fieldValueBetween(string $field, $min, $max): string
    {
        $min = self::formatValue($min);
        $max = self::formatValue($max);

        return "{$field} >= {$min} AND {$field} <= {$max}";
    }

    public static function fieldValueIn(string $field, array $haystack): string
    {
        $queryParts = array_map(
            function ($value) use ($field): string {
                $value = self::formatValue($value);

                return "{$field} == {$value}";
            },
            $haystack
        );

        return join(" OR ", $queryParts);
    }

    /**
     * @param int|DateTime $max
     */
    public static function fieldValueLessThan(string $field, $max): string
    {
        $max = self::formatValue($max);

        return "{$field} < {$max}";
    }

    /**
     * @param int|DateTime $max
     */
    public static function fieldValueLessThanOrEqualTo(string $field, $max): string
    {
        $max = self::formatValue($max);

        return "{$field} <= {$max}";
    }

    /**
     * @param int|DateTime $min
     */
    public static function fieldValueGreaterThan(string $field, $min): string
    {
        $min = self::formatValue($min);

        return "{$field} > {$min}";
    }

    /**
     * @param int|DateTime $min
     */
    public static function fieldValueGreaterThanOrEqualTo(string $field, $min): string
    {
        $min = self::formatValue($min);

        return "{$field} >= {$min}";
    }

    public static function formatValue($value): string
    {
        if (is_numeric($value)) {
            return (string)$value;
        }

        if (is_a($value, DateTime::class)) {
            return "DateTime({$value->format('Y,m,d,H,i,s')})";
        }

        return "\"{$value}\"";
    }
}
