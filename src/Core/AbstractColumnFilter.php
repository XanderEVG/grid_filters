<?php

namespace Xanderevg\GridFiltersLibrary\Core;

use Xanderevg\GridFiltersLibrary\Core\Exceptions\FilterOperatorException;

abstract class AbstractColumnFilter implements ColumnFilterInterface
{
    public function __construct(protected QueryBuilderInterface $builder, protected FilterElement $filterBy)
    {
        $this->filterBy->type = OperatorMapper::resolve($this->filterBy->type);
    }

    protected function checkAllowedOperator($operator, $allowed_operator): void
    {
        if (!in_array($operator, $allowed_operator)) {
            throw new FilterOperatorException("Invalid filter operator `$operator`");
        }
    }

    public function add(): QueryBuilderInterface
    {
        $column = $this->filterBy->column;
        $operator = $this->filterBy->operator;
        $value = $this->filterBy->value ?? null;
        $enabled = $this->filterBy->enabled ?? true;

        if (!$enabled) {
            return $this->builder;
        }

        if ('is null' === $operator) {
            return $this->builder->whereNull($column);
        }

        if ('is not null' === $operator) {
            return $this->builder->whereNotNull($column);
        }

        return $this->addColumnFilter($column, $operator, $value);
    }

    abstract protected function addColumnFilter(string $column, string $operator, mixed $value): mixed;
}
