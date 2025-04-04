<?php

namespace Xanderevg\GridFiltersLibrary\Core\Filters;

use Xanderevg\GridFiltersLibrary\Core\AbstractColumnFilter;
use Xanderevg\GridFiltersLibrary\Core\Exceptions\FilterValueException;

class SelectTreeFilter extends AbstractColumnFilter
{
    protected function addColumnFilter(string $column, string $operator, mixed $value): mixed
    {
        $this->checkAllowedOperator($operator, ['=', 'in']);

        if ('in' === $operator) {
            if (!is_array($value)) {
                throw new FilterValueException('Значение фильтра не является массивом');
            }
            $this->builder->whereIn($column, $value);

            return $this->builder;
        }

        if (is_array($value)) {
            $value = $value[0] ?? null;
        }
        $this->builder->where($column, $operator, $value);

        return $this->builder;
    }
}
