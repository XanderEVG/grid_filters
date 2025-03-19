<?php

namespace Xanderevg\GridFiltersLibrary\Symfony;

use Doctrine\ORM\QueryBuilder;
use Xanderevg\GridFiltersLibrary\Core\Exceptions\FilterValueException;
use Xanderevg\GridFiltersLibrary\Core\QueryBuilderInterface;

class DoctrineQueryBuilderAdapter implements QueryBuilderInterface
{
    public function __construct(private QueryBuilder $builder) {}

    public function getBuilder(): mixed
    {
        return $this->builder;
    }

    private function getPlaceholder(string $field, string|array $operator, $value): string
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        $value_hash = crc32($value);
        return str_replace('.', '_', $field).'_' . $operator . '_'.$value_hash;
    }

    public function where(string $field, string $operator, $value): self
    {
        $placeholder = $this->getPlaceholder($field, $operator, $value);
        $this->builder
            ->andWhere("$field $operator :$placeholder")
            ->setParameter($placeholder, $value)
        ;
        return $this;
    }

    public function whereNull(string $field): self
    {
        $this->builder->andWhere("$field is null");
        return $this;
    }

    public function whereNotNull(string $field): self
    {
        $this->builder->andWhere("$field is not null");
        return $this;
    }

    public function whereFalseOrNull(string $field): self
    {
        $this->builder->andWhere(
            $this->builder->expr()->orX(
                "$field = false",
                "$field is null"
            )
        );

        return $this;
    }

    public function whereDate(string $field, string $operator, string $value): self
    {
        $placeholder = $this->getPlaceholder($field, $operator, $value);

        if ($operator === '=') {
            $start = new \DateTimeImmutable($value . ' 00:00:00');
            $end = new \DateTimeImmutable($value . ' 23:59:59');

            $placeholder_start = $placeholder.'_start';
            $placeholder_end = $placeholder.'_end';

            $this->builder
                ->andWhere("$field BETWEEN :$placeholder_start AND :$placeholder_end")
                ->setParameter($placeholder_start, $start)
                ->setParameter($placeholder_end, $end)
            ;

            return $this;
        }

        if ($operator === '>') {
            $end = new \DateTimeImmutable($value . ' 23:59:59');
            $placeholder_end = $placeholder.'_end';

            $this->builder
                ->andWhere("$field > :$placeholder_end")
                ->setParameter($placeholder_end, $end)
            ;
            return $this;
        }

        if ($operator === '>=') {
            $start = new \DateTimeImmutable($value . ' 00:00:00');
            $placeholder_start = $placeholder.'_start';

            $this->builder
                ->andWhere("$field >= :$placeholder_start")
                ->setParameter($placeholder_start, $start)
            ;

            return $this;
        }

        if ($operator === '<') {
            $start = new \DateTimeImmutable($value . ' 00:00:00');
            $placeholder_start = $placeholder.'_start';

            $this->builder
                ->andWhere("$field < :$placeholder_start")
                ->setParameter($placeholder_start, $start)
            ;

            return $this;
        }
        if ($operator === '<=') {
            $end = new \DateTimeImmutable($value . ' 23:59:59');
            $placeholder_end = $placeholder.'_end';

            $this->builder
                ->andWhere("$field < :$placeholder_end")
                ->setParameter($placeholder_end, $end)
            ;

            return $this;
        }


        $this->builder
            ->andWhere("DATE($field) $operator :$placeholder")
            ->setParameter($placeholder, $value)
        ;
        return $this;
    }

    public function whereBetween(string $field, string $from, string $to): self {
        $placeholder = $this->getPlaceholder($field, 'BETWEEN', $from.$to);
        $placeholder_from = $placeholder.'_from';
        $placeholder_to = $placeholder.'_to';

        $this->builder
            ->andWhere("$field BETWEEN :$placeholder_from AND :$placeholder_to")
            ->setParameter($placeholder_from, $from)
            ->setParameter($placeholder_to, $to)
        ;
        return $this;
    }

    public function whereIn(string $field, array $value): self
    {
        if (empty($value)) {
            throw new FilterValueException('Values array cannot be empty for IN clause');
        }
        $placeholder = $this->getPlaceholder($field, 'in', $value);
        $this->builder->andWhere(
            $this->builder->expr()->in($field, ':'.$placeholder)
        )->setParameter($placeholder, $value);
        return $this;
    }


    public function whereNotIn(string $field, array $value): self
    {
        if (empty($value)) {
            throw new FilterValueException('Values array cannot be empty for NOT IN clause');
        }

        $placeholder = $this->getPlaceholder($field, 'not in', $value);
        $this->builder->andWhere(
            $this->builder->expr()->notIn($field, ':'.$placeholder)
        )->setParameter($placeholder, $value);

        return $this;
    }

    public function whereLike(string $field, string $value): self
    {
        $placeholder = $this->getPlaceholder($field, 'like', $value);
        $this->builder->andWhere(
            $this->builder->expr()->like($field, ':'.$placeholder)
        )->setParameter($placeholder, $value);
        return $this;
    }

    public function whereNotLike(string $field, string $value): self
    {
        $placeholder = $this->getPlaceholder($field, 'not like', $value);
        $this->builder->andWhere(
            $this->builder->expr()->notLike($field, ':'.$placeholder)
        )->setParameter($placeholder, $value);
        return $this;
    }
}