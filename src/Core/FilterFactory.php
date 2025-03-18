<?php

namespace Xanderevg\GridFiltersLibrary\Core;

use Xanderevg\GridFiltersLibrary\Core\Exceptions\FilterNotFoundException;

class FilterFactory
{
    protected string $baseNamespace;
    protected array $additionalNamespaces = [];
    private array $filtersCache = [];

    public function __construct(string $baseNamespace=__NAMESPACE__)
    {
        $this->baseNamespace = $baseNamespace;
    }

    public function create(QueryBuilderInterface $builder, FilterElement $filter): ColumnFilterInterface
    {
        $className = $this->resolveClassName($filter->type, $this->baseNamespace);

        return new $className($builder, $filter);
    }

    protected function resolveClassName(string $type, string $baseNamespace): string
    {
        if (isset($this->filtersCache[$type])) {
            return $this->filtersCache[$type];
        }

        $className = str_replace('_', '', ucwords($type, ' _')).'Filter';

        $baseFullClassName = $baseNamespace . '\\' . $className;
        if (class_exists($baseFullClassName)) {
            return $this->filtersCache[$type] = $baseFullClassName;
        }

        foreach ($this->additionalNamespaces as $namespace) {
            $customFullClassName = $namespace . '\\' . $className;
            if (class_exists($customFullClassName)) {
                return $this->filtersCache[$type] = $customFullClassName;
            }
        }

        throw new FilterNotFoundException("Unknown filter for type: {$type}");
    }

    public function addAdditionalFiltersNamespace(string $namespace): void
    {
        if (!in_array($namespace, $this->additionalNamespaces)) {
            $this->additionalNamespaces[] = rtrim($namespace, '\\');
            $this->clearCache();
        }
    }

    public function addAdditionalFiltersNamespaces(array $namespaces): void
    {
        $this->additionalNamespaces = $namespaces;
        $this->clearCache();
    }

    public function getAdditionalNamespaces(): array
    {
        return $this->additionalNamespaces;
    }

    private function clearCache()
    {
        $this->filtersCache = [];
    }
}