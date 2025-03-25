<?php

namespace Xanderevg\GridFiltersLibrary\Core\Filters;

use Xanderevg\GridFiltersLibrary\Core\AbstractColumnFilter;
use Xanderevg\GridFiltersLibrary\Core\Exceptions\FilterValueException;

class StringFilter extends AbstractColumnFilter
{
    protected function addColumnFilter(string $column, string $operator, mixed $value): mixed
    {
        $this->checkAllowedOperator($operator, ['=', '<>', 'like']);

        if ('like' === $operator) {
            $lowerCaseValue = mb_strtolower($value);
            $value = "%$lowerCaseValue%";

            return $this->builder->whereLike($column, $value);
        }

        if (null === $value) {
            if ('<>' === $operator) {
                return $this->builder->whereNotNull($column);
            }

            return $this->builder->whereNull($column);
        }

        if (!is_string($value)) {
            throw new FilterValueException();
        }

        $this->builder->where($column, $operator, $value);

        return $this->builder;
    }
}
