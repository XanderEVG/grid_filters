<?php


namespace Xanderevg\GridFiltersLibrary\Core\Filters;

use Xanderevg\GridFiltersLibrary\Core\AbstractColumnFilter;

class EditorFilter extends AbstractColumnFilter
{
    protected function addColumnFilter(string $column, string $operator, mixed $value): mixed
    {
        $this->checkAllowedOperator($operator, ['=', 'like', 'ilike']);

        // Затычка баги на фронте в либе z-q-lib
        $operator = 'like';

        if (in_array($operator, ['like', 'ilike'])) {
            $operator = 'ilike';
            $lowerCaseValue = mb_strtolower($value);
            $value = "%$lowerCaseValue%";
        }

        $this->builder->where($column, $operator, $value);

        return $this->builder;
    }
}
