<?php


namespace Xanderevg\GridFiltersLibrary\Core\Filters;

use Xanderevg\GridFiltersLibrary\Core\AbstractColumnFilter;
use Xanderevg\GridFiltersLibrary\Core\Exceptions\FilterValueException;

class NumberFilter extends AbstractColumnFilter
{
    protected function addColumnFilter(string $column, string $operator, mixed $value): mixed
    {
        $this->checkAllowedOperator($operator, ['=', '<', '>', '<=', '>=', 'in', '<>']);

        if ('in' === $operator) {
            $value = array_map(static fn ($v) => (int) $v, explode(',', $value));
            $this->builder->whereIn($column, $value);

            return $this->builder;
        }

        $value = str_replace(',', '.', $value);
        if (!is_numeric($value)) {
            throw new FilterValueException('Значение фильтра не является числом');
        }

        $this->builder->where($column, $operator, $value);

        return $this->builder;
    }
}
