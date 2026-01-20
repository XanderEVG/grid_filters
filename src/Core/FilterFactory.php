<?php

namespace Xanderevg\GridFiltersLibrary\Core;

use Xanderevg\GridFiltersLibrary\Core\Cache\ArrayCacheAdapter;
use Xanderevg\GridFiltersLibrary\Core\Cache\CacheAdapterInterface;
use Xanderevg\GridFiltersLibrary\Core\Exceptions\FilterNotFoundException;

class FilterFactory
{
    protected string $baseNamespace;
    protected array $additionalNamespaces = [];
    protected array $columnClassMap = [];
    private string $cacheKeyPrefix = 'filter_factory_';
    private CacheAdapterInterface $cacheAdapter;

    public function __construct(?string $baseNamespace = null, ?CacheAdapterInterface $cacheAdapter = null)
    {
        $this->baseNamespace = $baseNamespace ?? __NAMESPACE__.'\\Filters';
        $this->cacheAdapter = $cacheAdapter ?? new ArrayCacheAdapter();
    }

    public function create(QueryBuilderInterface $builder, FilterElement $filter): ColumnFilterInterface
    {
        $className = $this->resolveClassName($filter, $this->baseNamespace);

        return new $className($builder, $filter);
    }

    protected function resolveClassName(FilterElement $filter, string $baseNamespace): string
    {
        $type = $filter->type;
        $cacheKey = $this->generateCacheKey($filter);

        if ($cachedClass = $this->cacheAdapter->get($cacheKey)) {
            return $cachedClass;
        }

        // 1. Проверяем прямое соответствие колонки классу фильтра
        if (isset($this->columnClassMap[$filter->column])) {
            $directClass = $this->columnClassMap[$filter->column];
            if (class_exists($directClass)) {
                $this->cacheAdapter->set($cacheKey, $directClass);

                return $directClass;
            }
        }

        // 2. Проверяем стандартные фильтры
        $className = str_replace('_', '', ucwords($type, ' _')).'Filter';
        $baseFullClassName = $baseNamespace.'\\'.$className;
        if (class_exists($baseFullClassName)) {
            $this->cacheAdapter->set($cacheKey, $baseFullClassName);

            return $baseFullClassName;
        }

        // 3. Проверяем кастомные фильтры по типу
        foreach ($this->additionalNamespaces as $namespace) {
            $customFullClassName = $namespace.'\\'.$className;
            if (class_exists($customFullClassName)) {
                $this->cacheAdapter->set($cacheKey, $customFullClassName);

                return $customFullClassName;
            }
        }

        throw new FilterNotFoundException("Unknown filter for type: {$type}");
    }

    /**
     * Прямое указание класса фильтра для конкретной колонки
     * Пример: 'parentQuestion_id' => App\Filters\RelationFilter::class.
     */
    public function mapColumnToClass(string $column, string $className): self
    {
        $this->columnClassMap[$column] = $className;
        $this->cacheAdapter->clear();

        return $this;
    }

    /**
     * Прямое указание массива соответствий колонок классам фильтров.
     */
    public function mapColumnsToClasses(array $mappings): self
    {
        $this->columnClassMap = array_merge($this->columnClassMap, $mappings);
        $this->cacheAdapter->clear();

        return $this;
    }

    public function clearMappings(): self
    {
        $this->columnClassMap = [];
        $this->cacheAdapter->clear();

        return $this;
    }

    public function addAdditionalFiltersNamespace(string $namespace): self
    {
        if (!in_array($namespace, $this->additionalNamespaces, true)) {
            $this->additionalNamespaces[] = rtrim($namespace, '\\');
            $this->cacheAdapter->clear();
        }

        return $this;
    }

    public function addAdditionalFiltersNamespaces(array $namespaces): self
    {
        $this->additionalNamespaces = $namespaces;
        $this->cacheAdapter->clear();

        return $this;
    }

    public function getAdditionalNamespaces(): array
    {
        return $this->additionalNamespaces;
    }

    public function setCacheAdapter(CacheAdapterInterface $adapter): self
    {
        $this->cacheAdapter = $adapter;

        return $this;
    }

    private function generateCacheKey(FilterElement $filter): string
    {
        if ($this->columnClassMap[$filter->column] ?? null) {
            return $this->cacheKeyPrefix.md5(serialize([
                'column' => $filter->column,
                'type' => $filter->type,
                'base_namespace' => $this->baseNamespace,
                'additional_namespaces' => $this->additionalNamespaces,
            ]));
        }

        return $this->cacheKeyPrefix.md5(serialize([
            'type' => $filter->type,
            'base_namespace' => $this->baseNamespace,
            'additional_namespaces' => $this->additionalNamespaces,
        ]));
    }
}
