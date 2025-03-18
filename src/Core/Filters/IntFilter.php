<?php


namespace Xanderevg\GridFiltersLibrary\Core\Filters;

use Xanderevg\GridFiltersLibrary\Core\AbstractColumnFilter;
use Xanderevg\GridFiltersLibrary\Core\Exceptions\FilterValueException;

class IntFilter extends AbstractColumnFilter
{
    protected function addColumnFilter(string $column, string $operator, mixed $value): mixed
    {
        $this->checkAllowedOperator($operator, ['=', '<', '>', '<=', '>=', 'in', 'not in', '<>']);

        if ('in' === $operator) {
            if (!is_array($value)) {
                throw new FilterValueException('Значение фильтра не является массивом');
            }
            $this->builder->whereIn($column, $value);
        }

        if ('not in' === $operator) {
            if (!is_array($value)) {
                throw new FilterValueException('Значение фильтра не является массивом');
            }
            $this->builder->whereNotIn($column, $value);
        }

        // Массив идшников
        if (str_contains($value, ',') && in_array($operator, ['=', '<>'])) {
            $values = explode(',', $value);
            $values = array_map(fn ($v) => '' === trim($v) ? null : (int) $v, $values);
            $values = array_filter($values, static function ($v) {
                return null !== $v;
            });

            if ('=' === $operator) {
                $this->builder->whereIn($column, $values);
            } else {
                $this->builder->whereNotIn($column, $values);
            }

            return $this->builder;
        }

        if (!is_numeric($value)) {
            throw new FilterValueException('Значение фильтра не является числом');
        }

        $this->builder->where($column, $operator, $value);

        return $this->builder;
    }
}
