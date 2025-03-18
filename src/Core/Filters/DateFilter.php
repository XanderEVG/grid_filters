<?php

namespace Xanderevg\GridFiltersLibrary\Core\Filters;

use Xanderevg\GridFiltersLibrary\Core\AbstractColumnFilter;

class DateFilter extends AbstractColumnFilter
{
    protected function addColumnFilter(string $column, string $operator, mixed $value): mixed
    {
        $this->checkAllowedOperator($operator, ['=', '<', '>', '<=', '>=', '<>']);

        // Затычка баги на фронте в либе z-q-lib
        $operator = '>' === $operator ? '>=' : $operator;
        $operator = '<' === $operator ? '<=' : $operator;

        try {
            if ($value) {
                $value = trim($value);
            }
            $date = new \DateTime($value);
            $value = $date->format('Y-m-d');
        } catch (\Exception $e) {
            return $this->builder;
        }

        $this->builder->whereDate($column, $operator, $value);

        return $this->builder;
    }
}
