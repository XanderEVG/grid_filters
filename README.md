# Универсальная библиотека для фильтрации данных в таблицах (Laravel & Symfony)

## Описание
Библиотека предоставляет инструменты для динамического добавления условий фильтрации (WHERE) к 
запросам Laravel Eloquent Builder и Doctrine QueryBuilder на основе параметров, переданных с клиента 
(например, из UI таблицы).

Библиотека сократит время на реализацию фильтрации таблиц, унифицирует код для Laravel/Symfony и позволит легко масштабировать логику запросов.

Решение идеально подходит для:
- Админ-панелей с гридами.
- Приложений с табличными данными, требующими гибкой фильтрации.

Ключевые возможности
- Поддержка Laravel (Eloquent) и Symfony (Doctrine ORM) из коробки.
- Единый API для обеих платформ.
- Объединение фильтров с помощью логического оператора AND
- Фильтрация по любым полям модели/сущности.
- Несколько фильтров на одно поле.
- Комбинирование фильтров для разных полей.
- Поддержка кэша (Xanderevg\GridFiltersLibrary\Core\Cache\CacheAdapterInterface)

#### Поддержка типов: текст, число, дата, булево, диапазон, список и др.

## Сортировка
- Мультиколоночная сортировка.
- Настраиваемые направления (ASC/DESC).


## Пример использования
1. Создание фабрики
- `$baseFactory = new FilterFactory(); // Бибилиотечные фильтры`
- `$baseFactory = new FilterFactory('App\BaseFilters'); // BaseFilters`
- `$customFactory = (new FilterFactory())->addNamespace('App\CustomFilters'); //Библиотечные + CustomFilters, приоритет у библиотечных`
- `$customFactory = (new FilterFactory('App\BaseFilters'))->addNamespace('App\CustomFilters'); //BaseFilters + CustomFilters, приоритет у BaseFilters`


2. Интеграция с DI-контейнерами Laravel:
```
// В ServiceProvider
$this->app->singleton(FilterFactory::class, function() {
    return (new FilterFactory(config('filters.base_namespace')))->addNamespace(config('filters.custom_namespace'));
});
```

3.  Добавление фильтров
```
use FilterLibrary\Laravel\Query\EloquentBuilderAdapter;
use FilterLibrary\Symfony\Query\DoctrineQueryBuilderAdapter;
use FilterLibrary\Core\FilterFactory;

// Инициализация фабрики
$adapterLaravel = new EloquentBuilderAdapter($eloquentBuilder);
$adapterSymfony = new DoctrineQueryBuilderAdapter($doctrineQueryBuilder);

$factory = new FilterFactory('Xanderevg\GridFilters\Filters');
$factory->addNamespace('App\CustomFilters');

// Использование
$filterElements = [
    new FilterElement('column_1', 'value1', 'eq', 'string'),
    new FilterElement('column_2', 'value2', 'eq', 'string'),
];

try {   
    foreach ($filterElements as $filterElement) {
            $filter = FilterFactory->create($adapterLaravel, $filterElement);
            $adapterLaravel = $filter->add();
    }
} catch (FilterNotFoundException $e) {
    // Обработка ошибки
}

$builder = $adapterLaravel->getBuilder();
$builder->get();
    
```

4. FilterFacade приблизительно
```
class FilterFacade 
{
    private static ?FilterFactory $instance = null;

    public static function addNamespace(string $namespace): void
    {
        self::getInstance()->addNamespace($namespace);
    }

    public static function create(string $type, ...$args): object
    {
        return self::getInstance()->create($type, ...$args);
    }

    private static function getInstance(): FilterFactory
    {
        if (!self::$instance) {
            self::$instance = new FilterFactory(
                'Xanderevg\GridFilters\Filters'
            );
        }
        return self::$instance;
    }
}
```


## Поддерживаемые фильтры (из коробки)
- BooleanFilter
- BoolFilter
- DateFilter
- DatetimeFilter
- EditorFilter
- IdFilter
- IntFilter
- NumberFilter
- RolesFilter
- SelectFilter
- SelectTreeFilter
- StringFilter
- StringSelectFilter
- TextFilter

## Установка
`composer require xanderevg/grid_filters  `

## Кастомизация

## Адаптер для кэша
```
$redis = new Predis\Client();
$cacheAdapter = new App\Cache\RedisCacheAdapter($redis);

$filterFactory = new Xanderevg\GridFiltersLibrary\Core\FilterFactory(
    'Xanderevg\GridFiltersLibrary\Core\Filters',
    $cacheAdapter
);
```

## Лицензия
MIT License.

#### GitHub: https://github.com/xanderevg/grid_filters

# TODO
Тесты, включая тесты на реальной бд
