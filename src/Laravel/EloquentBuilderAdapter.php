<?php

namespace Xanderevg\GridFiltersLibrary\Laravel;

use Xanderevg\GridFiltersLibrary\Core\QueryBuilderInterface;
use Illuminate\Database\Eloquent\Builder;

class EloquentBuilderAdapter implements QueryBuilderInterface
{
    public function __construct(private Builder $builder) {}

    public function getBuilder(): mixed
    {
        return $this->builder;
    }

    public function where(string $field, string $operator, $value): self {
        $this->builder->where($field, $operator, $value);
        return $this;
    }

    public function whereBetween(string $field, $from, $to): self {
        $this->builder->whereBetween($field, [$from, $to]);
        return $this;
    }
}