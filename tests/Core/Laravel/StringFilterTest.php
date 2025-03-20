<?php

namespace Core\Laravel;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use PHPUnit\Framework\TestCase;
use Xanderevg\GridFiltersLibrary\Core\Exceptions\FilterOperatorException;
use Xanderevg\GridFiltersLibrary\Core\Exceptions\FilterValueException;
use Xanderevg\GridFiltersLibrary\Core\FilterElement;
use Xanderevg\GridFiltersLibrary\Core\FilterFactory;
use Xanderevg\GridFiltersLibrary\Laravel\EloquentBuilderAdapter;

class StringFilterTest extends TestCase
{
    private \PDO $pdo;
    private Builder $eloquentBuilder;
    private EloquentBuilderAdapter $adapterLaravel;
    private FilterFactory $baseFactory;

    public function setUp(): void
    {
        $dsn = 'pgsql:host=127.0.0.1;port=5432;dbname=grid_filters_test';
        $this->pdo = new \PDO($dsn, 'user','pass');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $connection = new Connection($this->pdo);
        $queryBuilder = new QueryBuilder($connection);
        $this->eloquentBuilder = new Builder($queryBuilder);
        $this->adapterLaravel = new EloquentBuilderAdapter($this->eloquentBuilder);
        $this->baseFactory = new FilterFactory();
    }

    public function testStringEq()
    {
        $filterElement = new FilterElement('column_1', 'value1', 'eq', 'string');
        $filter = $this->baseFactory->create($this->adapterLaravel, $filterElement);
        $filter->add();

        $wheres = $this->adapterLaravel->getBuilder()->getQuery()->wheres;
        $this->assertCount(1, $wheres);
        $this->assertArrayHasKey('column', $wheres[0]);
        $this->assertArrayHasKey('operator', $wheres[0]);
        $this->assertArrayHasKey('value', $wheres[0]);
        $this->assertEquals('column_1', $wheres[0]['column']);
        $this->assertEquals('=', $wheres[0]['operator']);
        $this->assertEquals('value1', $wheres[0]['value']);
    }


    public function testStringEqualSymbol()
    {
        $filterElement = new FilterElement('column_1', 'value1', '=', 'string');
        $filter = $this->baseFactory->create($this->adapterLaravel, $filterElement);
        $filter->add();

        $wheres = $this->adapterLaravel->getBuilder()->getQuery()->wheres;

        $this->assertCount(1, $wheres);
        $this->assertArrayHasKey('column', $wheres[0]);
        $this->assertArrayHasKey('operator', $wheres[0]);
        $this->assertArrayHasKey('value', $wheres[0]);
        $this->assertEquals('column_1', $wheres[0]['column']);
        $this->assertEquals('=', $wheres[0]['operator']);
        $this->assertEquals('value1', $wheres[0]['value']);
    }

    public function testStringLike()
    {
        $filterElement = new FilterElement('column_1', 'value1', 'like', 'string');
        $filter = $this->baseFactory->create($this->adapterLaravel, $filterElement);
        $filter->add();

        $wheres = $this->adapterLaravel->getBuilder()->getQuery()->wheres;

        $this->assertCount(1, $wheres);
        $this->assertArrayHasKey('column', $wheres[0]);
        $this->assertArrayHasKey('operator', $wheres[0]);
        $this->assertArrayHasKey('value', $wheres[0]);
        $this->assertEquals('column_1', $wheres[0]['column']);
        $this->assertEquals('ilike', $wheres[0]['operator']);
        $this->assertEquals('%value1%', $wheres[0]['value']);
    }

    public function testStringiLike()
    {
        $filterElement = new FilterElement('column_1', 'value1', 'ilike', 'string');
        $filter = $this->baseFactory->create($this->adapterLaravel, $filterElement);
        $filter->add();

        $wheres = $this->adapterLaravel->getBuilder()->getQuery()->wheres;
        $this->assertCount(1, $wheres);
        $this->assertArrayHasKey('column', $wheres[0]);
        $this->assertArrayHasKey('operator', $wheres[0]);
        $this->assertArrayHasKey('value', $wheres[0]);
        $this->assertEquals('column_1', $wheres[0]['column']);
        $this->assertEquals('ilike', $wheres[0]['operator']);
        $this->assertEquals('%value1%', $wheres[0]['value']);
    }


    public function testStringManyFilters()
    {
        $filterElement = new FilterElement('column_1', 'value', 'ilike', 'string');
        $filter = $this->baseFactory->create($this->adapterLaravel, $filterElement);
        $filter->add();

        $filterElement = new FilterElement('column_1', 'value_2', 'neq', 'string');
        $filter = $this->baseFactory->create($this->adapterLaravel, $filterElement);
        $filter->add();

        $filterElement = new FilterElement('column_1', 'value_3', '<>', 'string');
        $filter = $this->baseFactory->create($this->adapterLaravel, $filterElement);
        $filter->add();

        $filterElement = new FilterElement('column_2', 'value_1', 'eq', 'string');
        $filter = $this->baseFactory->create($this->adapterLaravel, $filterElement);
        $filter->add();

        $wheres = $this->adapterLaravel->getBuilder()->getQuery()->wheres;

        $this->assertCount(4, $wheres);
        $this->assertEquals('column_1', $wheres[0]['column']);
        $this->assertEquals('ilike', $wheres[0]['operator']);
        $this->assertEquals('%value%', $wheres[0]['value']);

        $this->assertEquals('column_1', $wheres[1]['column']);
        $this->assertEquals('<>', $wheres[1]['operator']);
        $this->assertEquals('value_2', $wheres[1]['value']);

        $this->assertEquals('column_1', $wheres[2]['column']);
        $this->assertEquals('<>', $wheres[2]['operator']);
        $this->assertEquals('value_3', $wheres[2]['value']);

        $this->assertEquals('column_2', $wheres[3]['column']);
        $this->assertEquals('=', $wheres[3]['operator']);
        $this->assertEquals('value_1', $wheres[3]['value']);
    }


    public function testStringBadOperator()
    {
        $this->expectException(FilterOperatorException::class);

        $filterElement = new FilterElement('column_1', 'value', '>', 'string');
        $filter = $this->baseFactory->create($this->adapterLaravel, $filterElement);
        $filter->add();

        $wheres = $this->adapterLaravel->getBuilder()->getQuery()->wheres;
    }

    public function testStringNullValue()
    {
        $filterElement = new FilterElement('column_1', null, '=', 'string');
        $filter = $this->baseFactory->create($this->adapterLaravel, $filterElement);
        $filter->add();

        $wheres = $this->adapterLaravel->getBuilder()->getQuery()->wheres;
        $this->assertEquals('column_1', $wheres[0]['column']);
        $this->assertEquals('Null', $wheres[0]['type']);
    }

    public function testStringNotNullValue()
    {
        $filterElement = new FilterElement('column_1', null, '<>', 'string');
        $filter = $this->baseFactory->create($this->adapterLaravel, $filterElement);
        $filter->add();

        $wheres = $this->adapterLaravel->getBuilder()->getQuery()->wheres;
        $this->assertEquals('column_1', $wheres[0]['column']);
        $this->assertEquals('NotNull', $wheres[0]['type']);
    }

    public function testStringBadValue()
    {
        $this->expectException(FilterValueException::class);

        $filterElement = new FilterElement('column_1', false, '=', 'string');
        $filter = $this->baseFactory->create($this->adapterLaravel, $filterElement);
        $filter->add();

        $wheres = $this->adapterLaravel->getBuilder()->getQuery()->wheres;
    }
}