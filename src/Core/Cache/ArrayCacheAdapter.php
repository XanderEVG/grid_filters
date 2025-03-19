<?php

namespace Xanderevg\GridFiltersLibrary\Core\Cache;

class ArrayCacheAdapter implements CacheAdapterInterface
{
    private array $cache = [];

    public function get(string $key): ?string
    {
        return $this->cache[$key] ?? null;
    }

    public function set(string $key, string $className): void
    {
        $this->cache[$key] = $className;
    }

    public function clear(): void
    {
        $this->cache = [];
    }
}
