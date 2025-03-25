<?php

namespace Core\Symfony;

use PHPUnit\Framework\TestCase;
use Xanderevg\GridFiltersLibrary\Core\FilterElement;
use Xanderevg\GridFiltersLibrary\Core\FilterFactory;
use Xanderevg\GridFiltersLibrary\Symfony\DoctrineQueryBuilderAdapter;

class StringFilterTest extends TestCase
{
    private DoctrineQueryBuilderAdapter $adapter;
    private FilterFactory $baseFactory;

    public function setUp(): void
    {
        // TODO Мокнуть нормально, что бы можно было получить мокнутый результат
        $builder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);

        $this->adapter = new DoctrineQueryBuilderAdapter($builder);
        $this->baseFactory = new FilterFactory();
    }

    public function testStringEq()
    {
        $filterElement = new FilterElement('column_1', 'value1', 'eq', 'string');
        $filter = $this->baseFactory->create($this->adapter, $filterElement);
        $adepter = $filter->add();

        $this->assertTrue(true, true);
    }
}
