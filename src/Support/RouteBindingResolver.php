<?php

namespace Laravel\Ranger\Support;

use Illuminate\Database\Eloquent\Model;

class RouteBindingResolver
{
    protected static $booted = [];

    protected static $columns = [];

    /**
     * @return array{type: string|null, key: string}
     */
    public static function resolveTypeAndKey(string $routable, $key): array
    {
        $booted = self::$booted[$routable] ??= app($routable);

        $key ??= $booted->getRouteKeyName();

        if (! $booted instanceof Model) {
            return [null, $key];
        }

        self::$columns[$routable] ??= $booted->getConnection()->getSchemaBuilder()->getColumns($booted->getTable());

        $firstColumn = collect(self::$columns[$routable])->first(
            fn ($column) => $column['name'] === $key,
        );

        return [$firstColumn['type_name'] ?? null, $key];
    }
}
