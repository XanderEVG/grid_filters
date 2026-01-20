<?php

namespace Core\Laravel;

use Illuminate\Database\Eloquent\Builder;
use PHPUnit\Framework\TestCase;
use Xanderevg\GridFiltersLibrary\Core\FilterElement;
use Xanderevg\GridFiltersLibrary\Core\FilterFactory;
use Xanderevg\GridFiltersLibrary\Laravel\EloquentBuilderAdapter;

class FilterByColumnFactoryTest extends TestCase
{
    public function testFilterCreationUsingColumnClassMap(): void
    {
        // Создаем mock для Builder
        $builder = $this->createMock(Builder::class);
        $adapter = new EloquentBuilderAdapter($builder);

        // Создаем маппинг колонок на классы фильтров
        $columnClassMap = [
            'customField' => 'Xanderevg\GridFiltersLibrary\Core\Filters\IdFilter',
            'created_at' => 'DatetimeFilter',
            'is_active' => 'BooleanFilter',
        ];

        // Создаем фабрику с маппингом
        $factory = (new FilterFactory(
            'Xanderevg\\GridFiltersLibrary\\Core\\Filters',
            null
        ))->mapColumnsToClasses($columnClassMap);


        // Создаем элемент фильтра для колонки email
        $filterElement = new FilterElement('customField', 'test@example.com', 'eq', 'string');

        // Получаем фильтр через фабрику
        $filter = $factory->create($adapter, $filterElement);

        // Проверяем, что создан правильный класс фильтра
        $this->assertInstanceOf(
            'Xanderevg\GridFiltersLibrary\Core\Filters\IdFilter',
            $filter
        );
    }
}
