<?php

namespace Laravel\Ranger\Components;

class InertiaResponse
{
    /**
     * @param  array<string, Type>  $data
     */
    public function __construct(
        public readonly string $component,
        public readonly array $data,
    ) {
        //
    }
}
