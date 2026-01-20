<?php

namespace Xanderevg\GridFiltersLibrary\Core\Filters;

use Xanderevg\GridFiltersLibrary\Core\AbstractColumnFilter;

class EditorFilter extends AbstractColumnFilter
{
    protected function addColumnFilter(string $column, string $operator, mixed $value): mixed
    {
        $this->checkAllowedOperator($operator, ['=', 'like']);

        // Затычка баги на фронте в либе z-q-lib
        $operator = 'like';

        if ('like' === $operator) {
            $lowerCaseValue = mb_strtolower($value);
            $value = "%$lowerCaseValue%";

            return $this->builder->whereLike($column, $value);
        }

        $this->builder->where($column, $operator, $value);

        return $this->builder;
    }
}
