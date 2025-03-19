<?php

namespace Xanderevg\GridFiltersLibrary\Core\Filters;

use Xanderevg\GridFiltersLibrary\Core\AbstractColumnFilter;

class RolesFilter extends AbstractColumnFilter
{
    protected function addColumnFilter(string $column, string $operator, mixed $value): mixed
    {
        $this->checkAllowedOperator($operator, ['=', '<>']);

        $this->builder->where($column, $operator, $value);

        return $this->builder;
    }
}
