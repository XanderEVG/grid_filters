<?php

namespace Xanderevg\GridFiltersLibrary\Core;

interface QueryBuilderInterface
{
    public function getBuilder(): mixed;

    public function where(string $field, string $operator, mixed $value): self;

    public function whereNull(string $field): self;

    public function whereNotNull(string $field): self;

    public function whereFalseOrNull(string $field): self;

    public function whereDate(string $field, string $operator, string $value): self;
    public function whereBetween(string $field, string $from, string $to): self;

    public function whereIn(string $field, array $value): self;
    public function whereNotIn(string $field, array $value): self;

    public function whereLike(string $field, string $value): self;
    public function whereNotLike(string $field, string $value): self;

    // whereMonth
    // whereDay
    // whereYear
    // whereTime
}