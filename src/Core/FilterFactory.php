<?php

namespace Xanderevg\GridFiltersLibrary\Core;

use Xanderevg\GridFiltersLibrary\Core\Cache\ArrayCacheAdapter;
use Xanderevg\GridFiltersLibrary\Core\Cache\CacheAdapterInterface;
use Xanderevg\GridFiltersLibrary\Core\Exceptions\FilterNotFoundException;

class FilterFactory
{
    protected string $baseNamespace;
    protected array $additionalNamespaces = [];
    private string $cacheKeyPrefix = 'filter_factory_';
    private CacheAdapterInterface $cacheAdapter;

    public function __construct(?string $baseNamespace = null, ?CacheAdapterInterface $cacheAdapter = null)
    {
        $this->baseNamespace = $baseNamespace ?? __NAMESPACE__.'\\Filters';
        $this->cacheAdapter = $cacheAdapter ?? new ArrayCacheAdapter();
    }

    public function create(QueryBuilderInterface $builder, FilterElement $filter): ColumnFilterInterface
    {
        $className = $this->resolveClassName($filter->type, $this->baseNamespace);

        return new $className($builder, $filter);
    }

    protected function resolveClassName(string $type, string $baseNamespace): string
    {
        $cacheKey = $this->generateCacheKey($type);

        if ($cachedClass = $this->cacheAdapter->get($cacheKey)) {
            return $cachedClass;
        }

        $className = str_replace('_', '', ucwords($type, ' _')).'Filter';
        $baseFullClassName = $baseNamespace.'\\'.$className;
        if (class_exists($baseFullClassName)) {
            $this->cacheAdapter->set($cacheKey, $baseFullClassName);

            return $baseFullClassName;
        }

        foreach ($this->additionalNamespaces as $namespace) {
            $customFullClassName = $namespace.'\\'.$className;
            if (class_exists($customFullClassName)) {
                $this->cacheAdapter->set($cacheKey, $customFullClassName);

                return $customFullClassName;
            }
        }

        throw new FilterNotFoundException("Unknown filter for type: {$type}");
    }

    public function addAdditionalFiltersNamespace(string $namespace): self
    {
        if (!in_array($namespace, $this->additionalNamespaces)) {
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

    private function generateCacheKey(string $type): string
    {
        return $this->cacheKeyPrefix.md5(serialize([
            $type,
            $this->baseNamespace,
            $this->additionalNamespaces,
        ]));
    }
}
