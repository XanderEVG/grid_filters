<?php

namespace Xanderevg\GridFiltersLibrary\Symfony;

use Doctrine\ORM\QueryBuilder;
use Xanderevg\GridFiltersLibrary\Core\QueryBuilderInterface;

class DoctrineQueryBuilderAdapter implements QueryBuilderInterface
{
    public function __construct(private QueryBuilder $builder) {}

    public function getBuilder(): mixed
    {
        return $this->builder;
    }

    public function where(string $field, string $operator, $value): self {
        $this->builder
            ->andWhere("$field $operator :value")
            ->setParameter('value', $value)
        ;
        return $this;
    }

    public function whereBetween(string $field, $from, $to): self {
        $this->builder
            ->andWhere("$field BETWEEN :from AND :to")
            ->setParameter('from', $from)
            ->setParameter('to', $to)
        ;
        return $this;
    }
}