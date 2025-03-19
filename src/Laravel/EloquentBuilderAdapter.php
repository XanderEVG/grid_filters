<?php

namespace Xanderevg\GridFiltersLibrary\Laravel;

use Illuminate\Database\Eloquent\Builder;
use Xanderevg\GridFiltersLibrary\Core\Exceptions\FilterValueException;
use Xanderevg\GridFiltersLibrary\Core\QueryBuilderInterface;

class EloquentBuilderAdapter implements QueryBuilderInterface
{
    public function __construct(private Builder $builder)
    {
    }

    public function getBuilder(): mixed
    {
        return $this->builder;
    }

    public function where(string $field, string $operator, $value): self
    {
        $this->builder->where($field, $operator, $value);

        return $this;
    }

    public function whereNull(string $field): self
    {
        $this->builder->whereNull($field);

        return $this;
    }

    public function whereNotNull(string $field): self
    {
        $this->builder->whereNotNull($field);

        return $this;
    }

    public function whereFalseOrNull(string $field): self
    {
        $this->builder->where(function ($query, $field) {
            /* @var Builder $query */
            $query->where($field, '=', false)->whereNull($field);
        });

        return $this;
    }

    public function whereDate(string $field, string $operator, string $value): self
    {
        $this->builder->whereDate($field, $operator, $value);

        return $this;
    }

    public function whereBetween(string $field, $from, $to): self
    {
        $this->builder->whereBetween($field, [$from, $to]);

        return $this;
    }

    public function whereIn(string $field, array $value): self
    {
        if (empty($value)) {
            throw new FilterValueException('Values array cannot be empty for IN clause');
        }

        $this->builder->whereIn($field, $value);

        return $this;
    }

    public function whereNotIn(string $field, array $value): self
    {
        if (empty($value)) {
            throw new FilterValueException('Values array cannot be empty for NOT IN clause');
        }

        $this->builder->whereNotIn($field, $value);

        return $this;
    }

    public function whereLike(string $field, string $value): self
    {
        $this->builder->whereLike($field, $value);

        return $this;
    }

    public function whereNotLike(string $field, string $value): self
    {
        $this->builder->whereNotLike($field, $value);

        return $this;
    }
}
