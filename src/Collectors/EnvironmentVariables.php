<?php

namespace Laravel\Ranger\Collectors;

use Illuminate\Support\Collection;
use Laravel\Ranger\Components\EnvironmentVariable;

class EnvironmentVariables extends Collector
{
    public function collect(): Collection
    {
        $envPath = base_path('.env');

        if (! file_exists($envPath)) {
            return collect();
        }

        $envFile = file_get_contents($envPath);

        return collect($_ENV)
            ->filter(fn ($value, $key) => preg_match('/^'.$key.'=/m', $envFile) === 1)
            ->map(fn ($value, $key) => new EnvironmentVariable($key, $value));
    }
}
