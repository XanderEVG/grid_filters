<?php

namespace Xanderevg\GridFiltersLibrary\Core\Cache;

interface CacheAdapterInterface
{
    public function get(string $key): ?string;

    public function set(string $key, string $className): void;

    public function clear(): void;
}
