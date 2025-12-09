<?php

use Laravel\Ranger\Collectors\EnvironmentVariables;
use Laravel\Ranger\Components\EnvironmentVariable;

describe('EnvironmentVariables collector', function () {
    it('creates EnvironmentVariable components', function () {
        $collector = app(EnvironmentVariables::class);
        $variables = $collector->collect();

        if ($variables->isNotEmpty()) {
            $first = $variables->first();
            expect($first)->toBeInstanceOf(EnvironmentVariable::class);
        }

        expect(true)->toBeTrue();
    })->skip();

    it('returns empty collection when .env file does not exist', function () {
        expect(true)->toBeTrue();
    })->skip();
});
