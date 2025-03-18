<?php

namespace Xanderevg\GridFiltersLibrary\Core;

use Xanderevg\GridFiltersLibrary\Core\Exceptions\FilterOperatorException;

class OperatorMapper
{
    private const OPERATOR_MAP = [
        // Основные операторы
        'eq'  => '=',
        'neq' => '<>',
        'gt'  => '>',
        'gte' => '>=',
        'lt'  => '<',
        'lte' => '<=',

        // Синонимы
        'is' => 'is',
        'is_true' => 'is true',
        'is_false' => 'is false',

        'like' => 'like',
        'ilike' => 'ilike',

        'in' => 'in',
        'not_in' => 'not in',

        'is_null' => 'is null',
        'is_not_null' => 'not null',
    ];

    public static function resolve(string $alias): string
    {
        if (!isset(self::OPERATOR_MAP[$alias])) {
            if (in_array($alias, self::OPERATOR_MAP)) {
                return $alias;
            }
            throw new FilterOperatorException("Invalid filter operator: $alias");
        }
        return self::OPERATOR_MAP[$alias];
    }

    public static function getSupportedOperators(): array {
        return array_unique(array_merge(array_keys(self::OPERATOR_MAP), array_values(self::OPERATOR_MAP)));
    }

    public static function isSupported(string $alias): bool {
        return isset(self::OPERATOR_MAP[$alias]) || in_array($alias, self::OPERATOR_MAP);
    }
}