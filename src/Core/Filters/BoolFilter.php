<?php

namespace Xanderevg\GridFiltersLibrary\Core\Filters;

use Xanderevg\GridFiltersLibrary\Core\AbstractColumnFilter;

class BoolFilter extends AbstractColumnFilter
{
    protected function addColumnFilter(string $column, string $operator, mixed $value): mixed
    {
        $this->checkAllowedOperator($operator, ['=', '<>', 'is', 'is true', 'is false']);

        if ($operator === 'is true') {
            return $this->builder->where($column, '=', true);
        }

        if ($operator === 'is false') {
            return $this->builder->where($column, '=', false);
        }

        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        if ($operator === '<>') {
            $value = !$value;
        }

        if (true === $value) {
            return $this->builder->where($column, '=', true);
        }

        return $this->builder->whereFalseOrNull($column);
    }
}
