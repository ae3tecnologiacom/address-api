<?php

namespace App\Base\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheTrait {
    /**
     * @param string $key
     * @param callable $callback
     * @param int|null $minutes
     * @return mixed
     */
    public function cache(string $key, callable $callback, ?int $minutes = null): mixed {
        try {
            return Cache::remember($key, $minutes ?? config('api.cache.ttl'), $callback);
        } catch (\Predis\Connection\ConnectionException $exception) {
            return $callback();
        }
    }

}
