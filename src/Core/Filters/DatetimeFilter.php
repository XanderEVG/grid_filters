<?php

namespace Xanderevg\GridFiltersLibrary\Core\Filters;

use Xanderevg\GridFiltersLibrary\Core\AbstractColumnFilter;

class DatetimeFilter extends AbstractColumnFilter
{
    protected function addColumnFilter(string $column, string $operator, mixed $value): mixed
    {
        $this->checkAllowedOperator($operator, ['=', '<', '>', '<=', '>=', '<>']);

        try {
            $date = new \DateTime($value);
            $value = $date->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return $this->builder;
        }

        $this->builder->where($column, $operator, $value);

        return $this->builder;
    }
}
