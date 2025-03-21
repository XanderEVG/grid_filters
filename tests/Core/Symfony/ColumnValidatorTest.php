<?php

namespace Core\Symfony;

use Xanderevg\GridFiltersLibrary\Core\Exceptions\FilterColumnException;
use Xanderevg\GridFiltersLibrary\Core\FilterElement;
use Xanderevg\GridFiltersLibrary\Core\FilterFactory;
use PHPUnit\Framework\TestCase;
use Xanderevg\GridFiltersLibrary\Symfony\DoctrineQueryBuilderAdapter;
use Doctrine\ORM\QueryBuilder;

class ColumnValidatorTest extends TestCase
{
    private DoctrineQueryBuilderAdapter $adapter;
    private FilterFactory $baseFactory;

    public function setUp(): void
    {
        $builder = $this->createMock(QueryBuilder::class);

        $this->adapter = new DoctrineQueryBuilderAdapter($builder);
        $this->baseFactory = new FilterFactory();
    }

    public static function correctColumnsProvider(): array
    {
        return [
            ['c'],
            ['ccc'],
            ['c.c'],
            ['column'],
            ['column1'],
            ['column_1'],
            ['column_1_1'],
            ['column_1_1_first'],
            ['column_first'],
            ['column__first'],
            ['e.column'],
            ['e.column_1'],
            ['e.column_1_1'],
            ['e.column_1_1_1'],
            ['entity.column_1'],
            ['entity_1.column_1'],
            ['entity_1_1.column_1'],
        ];
    }

    /**
     * @dataProvider correctColumnsProvider
     */
    public function testCorrectColumn($columnsName)
    {
        $filterElement = new FilterElement($columnsName, 'value1', 'eq', 'string');
        $filter = $this->baseFactory->create($this->adapter, $filterElement);
        $builder = $filter->add();

        $this->assertInstanceOf(DoctrineQueryBuilderAdapter::class, $builder);
    }


    public static function incorrectColumnsProvider(): array
    {
        return [
            ['e..column'],
            ['.column'],
            ['column.'],
            ['col-umn.'],
            [';column.'],
            ["'column"],
            ["\'column"],
            ["--column"],
            [",column"],
            ["co,lumn"],
            ["'--drop database"],
            ["'--"],
            ["sdfsd*sdf"],
            ["column/"],
            ["column or"],
            ["column or true=true"],
            ["*"],
        ];
    }

    /**
     * @dataProvider incorrectColumnsProvider
     */
    public function testIncorrectColumn($columnName)
    {
        $this->expectException(FilterColumnException::class);

        $filterElement = new FilterElement($columnName, 'value1', 'eq', 'string');
        $filter = $this->baseFactory->create($this->adapter, $filterElement);
        $filter->add();
    }
}