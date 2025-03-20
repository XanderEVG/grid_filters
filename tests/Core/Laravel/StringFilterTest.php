<?php

namespace Core\Laravel;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use PHPUnit\Framework\TestCase;
use Xanderevg\GridFiltersLibrary\Core\FilterElement;
use Xanderevg\GridFiltersLibrary\Core\FilterFactory;
use Xanderevg\GridFiltersLibrary\Laravel\EloquentBuilderAdapter;

class StringFilterTest extends TestCase
{
    private \PDO $pdo;
    private Builder $eloquentBuilder;

    public function setUp(): void
    {
        $dsn = 'pgsql:host=127.0.0.1;port=5432;dbname=grid_filters_test';
        $this->pdo = new \PDO($dsn, 'user','pass');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $connection = new Connection($this->pdo);
        $queryBuilder = new QueryBuilder($connection);
        $this->eloquentBuilder = new Builder($queryBuilder);
    }

    public function testStringEq()
    {
        $adapterLaravel = new EloquentBuilderAdapter($this->eloquentBuilder);
        $baseFactory = new FilterFactory();
        $filterElement = new FilterElement('column_1', 'value1', 'eq', 'string');
        $filter = $baseFactory->create($adapterLaravel, $filterElement);
        $adapterLaravel = $filter->add();

        /**
         * @var Builder $laravel_builder;
         */
        $laravel_builder = $adapterLaravel->getBuilder();
        $wheres = $laravel_builder->getQuery()->wheres;

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
        $adapterLaravel = new EloquentBuilderAdapter($this->eloquentBuilder);
        $baseFactory = new FilterFactory();
        $filterElement = new FilterElement('column_1', 'value1', '=', 'string');
        $filter = $baseFactory->create($adapterLaravel, $filterElement);
        $adapterLaravel = $filter->add();

        /**
         * @var Builder $laravel_builder;
         */
        $laravel_builder = $adapterLaravel->getBuilder();
        $wheres = $laravel_builder->getQuery()->wheres;

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
        $adapterLaravel = new EloquentBuilderAdapter($this->eloquentBuilder);
        $baseFactory = new FilterFactory();
        $filterElement = new FilterElement('column_1', 'value1', 'like', 'string');
        $filter = $baseFactory->create($adapterLaravel, $filterElement);
        $adapterLaravel = $filter->add();

        /**
         * @var Builder $laravel_builder;
         */
        $laravel_builder = $adapterLaravel->getBuilder();
        $wheres = $laravel_builder->getQuery()->wheres;
        var_dump($wheres);

        $this->assertCount(1, $wheres);
        $this->assertArrayHasKey('column', $wheres[0]);
        $this->assertArrayHasKey('operator', $wheres[0]);
        $this->assertArrayHasKey('value', $wheres[0]);
        $this->assertEquals('column_1', $wheres[0]['column']);
        $this->assertEquals('ilike', $wheres[0]['operator']);
        $this->assertEquals('%value1%', $wheres[0]['value']);
    }

    public function testStringLike()
    {
        $adapterLaravel = new EloquentBuilderAdapter($this->eloquentBuilder);
        $baseFactory = new FilterFactory();
        $filterElement = new FilterElement('column_1', 'value1', 'like', 'string');
        $filter = $baseFactory->create($adapterLaravel, $filterElement);
        $adapterLaravel = $filter->add();

        /**
         * @var Builder $laravel_builder;
         */
        $laravel_builder = $adapterLaravel->getBuilder();
        $wheres = $laravel_builder->getQuery()->wheres;
        var_dump($wheres);

        $this->assertCount(1, $wheres);
        $this->assertArrayHasKey('column', $wheres[0]);
        $this->assertArrayHasKey('operator', $wheres[0]);
        $this->assertArrayHasKey('value', $wheres[0]);
        $this->assertEquals('column_1', $wheres[0]['column']);
        $this->assertEquals('ilike', $wheres[0]['operator']);
        $this->assertEquals('%value1%', $wheres[0]['value']);
    }
}